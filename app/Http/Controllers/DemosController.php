<?php

namespace App\Http\Controllers;

use App\DemoConnector;
use App\Http\Requests\DeleteDemo;
use App\Http\Requests\StoreDemo;
use App\Models\Demo;
use App\Models\Solution;
use App\Models\SolutionVersion;
use App\ReferrerRedirector;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Throwable;

/**
 * Class DemoController
 * @package App\Http\Controllers
 */
class DemosController extends Controller {

    /**
     * Anzeigen einer laufenden Lösung-Demo
     * @param Demo $demo
     * @return Application|ResponseFactory|RedirectResponse|Response|Redirector
     */
    public function show(Demo $demo) {
        if ($demo->isFinished()) {
            return redirect()->to(request('ref') ?? route('index'))->with('info', 'Die Demo existiert nicht mehr.');
        }

        try {
            $connector = new DemoConnector($demo);
            $magicLink = $connector->magicLink();
        }
        catch (Throwable $exception) {
            report($exception);

            return redirect($demo->main ? $demo->solution->authorProfileSolutionPath() : route('index'))->with('error', 'Leider kann die Anfrage aktuell nicht verarbeitet werden.');
        }

        $viewName = $demo->main ? 'demos.show-admin' : 'demos.show-user';

        return response(view($viewName, [
            'solution' => $demo->solution,
            'demo' => $demo,
            'magicLink' => $magicLink,
            'version' => $demo->solutionVersion->version,
            'name' => $demo->solution->full_namespace . ' - Demo',
            'title' => $demo->main ? 'Admin-Demo - ' . $demo->solutionVersion->full_namespace : 'Benutzer-Demo - ' . $demo->solutionVersion->full_namespace
        ]));
    }

    /**
     * Simulation erstellen
     * @param StoreDemo $request
     * @param SolutionVersion $solutionVersion
     * @return RedirectResponse
     * @throws Exception
     * @throws GuzzleException
     */
    public function store(StoreDemo $request, SolutionVersion $solutionVersion) {
        // Demo erstellen. Die Id wird als Environment-Name in der Allisa App genommen.
        $validated = $request->validated();

        // Prüfen ob der Benutzer bereits eine Simulation hat.
        if ($solutionVersion->runningUserDemo() instanceof Demo) {
            return redirect(route('solution.demo', ['solution' => $solutionVersion->solution, 'version' => $solutionVersion->version]) . '?ref=' . $validated['ref'] ?? '');
        }

        try {
            $mainConnector = new DemoConnector($solutionVersion->mainDemo());
            $demoId = $mainConnector->copyMainDemo();
            $demo = Demo::create([
                'id' => $demoId,
                'user_id' => auth()->user()?->id,
                'organisation_id' => $validated['organisation_id'] ?? null,
                'license_id' => $validated['license_id'] ?? null,
                'solution_id' => $solutionVersion->solution->id,
                'solution_version_id' => $solutionVersion->id,
                'allisa_user_id' => config('allisa.simulation.user_id'),
            ]);

            $connector = new DemoConnector($demo);
            $token = $connector->generateAccessToken();
            $demo->update(['token' => $token]);
        }
        catch (BadResponseException $exception) {
            report($exception);

            return back()->with('error', 'Leider kann die Anfrage aktuell nicht verarbeitet werden.');
        }

        return redirect(route('demo.show', ['demo' => $demo]) . '?ref=' . $validated['ref'] ?? '');
    }

    /**
     * Eine laufende Demo beenden.
     * @param DeleteDemo $request
     * @param Demo $demo
     * @return Application|RedirectResponse|Redirector
     */
    public function delete(DeleteDemo $request, Demo $demo) {
        /* @var Solution $solution */
        $solution = $demo->solution()->first();

        if (!$solution?->version($demo->solutionVersion->version)) {
            return redirect(route('index'))->with('error', 'Lösung existiert nicht.');
        }

        if ($demo->isRunning()) {
            $connector = new DemoConnector($demo);

            try {
                // Bei Allisa die Umgebung und DB löschen
                $connector->deleteAllisaLiveDemo();

                // Als beendet markieren und exportierten Prozess löschen.
                $demo->markAsFinished();
            }
            catch (Throwable $throwable) {
                // Ignore
                report($throwable);

                return back()->with('error', 'Error. Demo könnte nicht beendet werden.');
            }
        }

        $ref = $request->validated()['ref'] ?? null;

        // Live-Demo vom Benutzer oder von einer Organisation gestartet.
        $demoOrigin = $demo->organisation ?? $demo->user;

        return redirect(ReferrerRedirector::to(base64_decode($ref), $demoOrigin->profileSolutionsPath()))->with('success', 'Live-Demo beendet');
    }
}
