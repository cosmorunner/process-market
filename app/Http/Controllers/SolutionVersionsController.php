<?php

namespace App\Http\Controllers;

use App\DemoConnector;
use App\Http\Requests\PublishSolutionVersion;
use App\Models\Demo;
use App\Models\SolutionVersion;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class SolutionVersionController
 * @package App\Http\Controllers
 */
class SolutionVersionsController extends Controller {

    /**
     * Eine Solution-Version fertigstellen
     * @param SolutionVersion $solutionVersion
     * @return Factory|RedirectResponse
     */
    public function complete(SolutionVersion $solutionVersion) {
        // Prüfen ob Version bereits publiziert wurde
        if ($solutionVersion->published_at) {
            return back()->with('warning', 'Die Version wurde bereits veröffentlicht.');
        }

        return view('solution-versions.complete', [
            'solutionVersion' => $solutionVersion,
            'graphs' => $solutionVersion->processVersions(),
            'solution' => $solutionVersion->solution,
            'name' => $solutionVersion->solution->full_namespace . ' - Fertigstellen'
        ]);
    }

    /**
     * Eine Läsung-Version mit einem hinzugefügten Allisa Systemen synchronisieren.
     * @param SolutionVersion $solutionVersion
     * @return Factory|Application|View
     */
    public function sync(SolutionVersion $solutionVersion) {
        if ($solutionVersion->solution->authoredByOrganisation()) {
            $createSystemRoute = route('organisation.settings.system.create', $solutionVersion->solution->author);
        }
        else {
            $createSystemRoute = route('settings.system.create');
        }

        return view('solution-versions.sync', [
            'createSystemRoute' => $createSystemRoute,
            'solutionVersion' => $solutionVersion,
            'systems' => $solutionVersion->solution->author->systems()->with('synchronizations')->get(),
        ]);
    }

    /**
     * Patch-Request. Eine Prozess-Version fertigstellen und neue "In der Entwicklung"-Version anlegen.
     * @param PublishSolutionVersion $request
     * @param SolutionVersion $solutionVersion
     * @return RedirectResponse
     * @throws Throwable
     */
    public function publish(PublishSolutionVersion $request, SolutionVersion $solutionVersion) {
        $validated = $request->validated();
        $version = $validated['version'];
        $changelog = $validated['changelog'] ?? null;

        // Prüfen ob Version bereits publiziert wurde
        if ($solutionVersion->published_at) {
            return back()->with('warning', 'Die Version wurde bereits veröffentlicht.');
        }

        try {
            DB::beginTransaction();

            $currentMainDemo = $solutionVersion->mainDemo();
            $connector = new DemoConnector($currentMainDemo);

            // Main Demo der publizierten Version beenden
            $solutionVersion->mainDemo()->markAsFinished();

            // Version publizieren. Eine neue "in der Entwicklung" Version wird angelegt.
            $newSolutionVersion = $solutionVersion->publish($version, $changelog);

            // Main Demo der neuen Version erstellen.
            $demoId = $connector->copyMainDemo();

            // Main Demo der neuen Version erstellen.
            $newMainDemo = Demo::createMainDemo($newSolutionVersion, $demoId);

            // Token aktualisieren
            $newMainDemo->update(['token' => $currentMainDemo->token]);

            DB::commit();

            return redirect(route('solution.versions', ['solution' => $solutionVersion->solution]))->with('success', 'Herzlichen Glückwunsch! Sie haben die Version "' . $version . '" fertiggestellt.');

        }
        catch (Exception $exception) {
            DB::rollBack();

            report($exception);

            return back()->with('error', 'Die Anfrage konnte leider nicht verarbeitet werden. Bitte versuchen Sie es später erneut.');
        }
    }

}
