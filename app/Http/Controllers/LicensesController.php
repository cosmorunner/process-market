<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteLicenseSync;
use App\Http\Requests\StoreProcessLicenseSimulation;
use App\Interfaces\Syncable;
use App\Loaders\PipeLoader;
use App\Models\Demo;
use App\Models\Environment;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\Solution;
use App\Models\SolutionVersion;
use App\Models\System;
use App\ReferrerRedirector;
use App\SimulationConnector;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

/**
 * Class LicensesController
 * @package App\Http\Controllers
 */
class LicensesController extends Controller {

    /**
     * Demo für die Resource einer Lizenz starten..
     * @param License $license
     * @param string $version
     * @return RedirectResponse|\Illuminate\View\View
     */
    public function demo(License $license, string $version) {
        /* @var Process|Solution $resource */
        $resource = $license->resource;

        if (!$versionModel = $resource->version($version, true)) {
            return redirect(route('index'))->with('error', $resource instanceof Process ? 'Prozess existiert nicht.' : 'Lösung existiert nicht.');
        }

        // Due to the possible redirects here, set the flash messages again.
        session()->reflash();

        $ref = request('ref') ?? request()->query('ref', '');

        // Process
        if ($resource instanceof Process) {
            /* @var ProcessVersion $versionModel */
            if (!$simulation = $versionModel->runningUserSimulation()) {
                return redirect(route('license.start_process_demo', [
                        'license' => $license,
                        'version' => $versionModel->version
                    ]) . '?ref=' . $ref);
            }

            return redirect(route('simulation.show', ['simulation' => $simulation]) . '?ref=' . $ref);
        }
        // Solution
        else {
            /* @var SolutionVersion $versionModel */
            if (!$simulation = $versionModel->runningUserDemo()) {
                return redirect(route('license.start_solution_demo', [
                        'license' => $license,
                        'version' => $versionModel->version
                    ]) . '?ref=' . $ref);
            }

            return redirect(route('demo.show', ['demo' => $simulation]) . '?ref=' . $ref);
        }
    }

    /**
     * Eine neue Demo eines Prozesses starten.
     * @param License $license
     * @param string $version
     * @return Application|Factory|View|RedirectResponse|\Illuminate\View\View
     */
    public function startProcessDemo(License $license, string $version) {
        /* @var Process $process */
        $process = $license->resource;

        if (!$versionModel = $process->version($version, true)) {
            return redirect(ReferrerRedirector::to(base64_decode(request('ref')), $license->owner))->with('error', 'Version existiert nicht.');
        }

        // Falls diese URL manuell aufgerufen wird, obwohl bereits eine Simulation existiert.
        if ($versionModel->runningUserSimulation() instanceof Simulation) {
            return redirect(route('license.demo', ['license' => $license, 'version' => $version]));
        }

        return view('licenses.start-process-demo', [
            'license' => $license,
            'versionModel' => $versionModel,
            'title' => $process->full_namespace . ' - Demo starten',
            'organisation' => $license->owner instanceof Organisation ? $license->owner : null
        ]);
    }

    /**
     * Eine neue Demo einer Lösung starten.
     * @param License $license
     * @param string $version
     * @return Application|Factory|View|RedirectResponse|\Illuminate\View\View
     */
    public function startSolutionDemo(License $license, string $version) {
        /* @var Solution $solution */
        $solution = $license->resource;

        if (!$versionModel = $solution->version($version, true)) {
            return redirect(ReferrerRedirector::to(base64_decode(request('ref')), $solution->authorProfileSolutionPath()))->with('error', 'Version existiert nicht.');
        }

        // Falls diese URL manuell aufgerufen wird, obwohl bereits eine Simulation existiert.
        if ($versionModel->runningUserDemo() instanceof Demo) {
            return redirect(route('license.demo', ['license' => $license, 'version' => $version]));
        }

        return view('licenses.start-solution-demo', [
            'license' => $license,
            'versionModel' => $versionModel,
            'title' => $solution->full_namespace . ' - Demo starten',
            'organisation' => $license->owner instanceof Organisation ? $license->owner : null
        ]);
    }

    /**
     * Simulation anhand einer Lizenz erstellen.
     * @param StoreProcessLicenseSimulation $request
     * @param License $license
     * @param string $version
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function storeProcessDemo(StoreProcessLicenseSimulation $request, License $license, string $version) {
        // Simulation erstellen. Die Id wird als Environment-Name in der Allisa App genommen.
        $validated = $request->validated();
        $process = $license->resource;
        $processVersion = $process->version($version, true);

        if (!$process instanceof Process) {
            return back()->with('error', 'Ungültige Anfrage.');
        }

        $role = $processVersion->definition->role($validated['role_id'] ?? null);

        $environmentId = $validated['environment_id'] ?? null;
        $environment = $environmentId ? Environment::find($environmentId) : null;

        $queryContext = PipeLoader::withoutTitle($environment?->query_context);

        // Prüfen ob der Benutzer bereits eine Simulation hat.
        if ($processVersion->runningUserSimulation() instanceof Simulation) {
            return redirect(route('process.demo', [
                'process' => $processVersion->process,
                'version' => $processVersion->version
            ]) . '?ref=' . $validated['ref'] ?? '');
        }

        $simulation = Simulation::create([
            'user_id' => auth()->user()->id,
            'organisation_id' => $validated['organisation_id'] ?? null,
            'license_id' => $validated['license_id'] ?? null,
            'process_id' => $processVersion->process->id,
            'process_version_id' => $processVersion->id,
            'allisa_user_id' => config('allisa.simulation.user_id'),
            'context' => $queryContext
        ]);

        $userEmail = $validated['user_email'] ?? 'demo@example.com';
        try {
            $connector = new SimulationConnector($simulation);
            $connector->instantiateSimulation($userEmail, $role, [], $environment);
        }
        catch (RequestException $exception) {
            report($exception);

            if (isset($connector->token)) {
                $connector->deleteAllisaSimulation();
            }

            $simulation->delete();

            return back()->with('error', 'Leider kann die Anfrage aktuell nicht verarbeitet werden.');
        }

        return redirect(route('simulation.show', ['simulation' => $simulation]) . '?ref=' . $validated['ref'] ?? '');
    }

    /**
     * Anzeige eines Benutzer-Profils.
     * @param License $license
     * @return Application|Factory|View
     */
    public function versions(License $license) {
        $user = Auth::user();

        $viewName = 'licenses.versions';
        $viewData = [
            'license' => $license,
            'systems' => $user->systems,
            'title' => 'Lizenz - ' . $license->resource->full_namespace . ' - Versionen'
        ];

        if ($license->resource instanceof Process) {
            $process = $license->resource()->with([
                'versions' => fn(HasMany $builder) => $builder->where('published_at', '!=', null),
                'versions.synchronizations' => fn(MorphMany $builder) => $builder->where('license_id', '=', $license->id),
                'versions.synchronizations.system'
            ])->first();

            $viewName = 'licenses.versions-process';
            $viewData = array_merge($viewData, ['process' => $process]);
        }

        if ($license->resource instanceof Solution) {
            $versions = $license->resource->versions()->latest()->where('published_at', '!=', null)->get();
            $fulfilledVersions = $versions->filter(fn(SolutionVersion $solutionVersion) => $solutionVersion->licenseOwnerHasAllProcessLicenses($license->owner));
            $publicVersions = $versions->filter(fn(SolutionVersion $solutionVersion) => !$solutionVersion->hasPrivateProcess());

            $license->resource->setRelation('versions', $fulfilledVersions);
            $viewName = 'licenses.versions-solution';
            $viewData = array_merge($viewData, [
                'solution' => $license->resource,
                'latestPublicVersion' => $publicVersions->first(),
                'missingVersions' => $publicVersions->count() !== $fulfilledVersions->count()
            ]);
        }

        return view($viewName, $viewData);
    }

    /**
     * Die Resource einer Lizenz zu einer Allisa Plattform exportieren
     * @param License $license
     * @param string $version
     * @return Application|RedirectResponse|Redirector
     */
    public function showSync(License $license, string $version) {
        /* @var ProcessVersion $processVersion */
        /* @var Process $process */
        $process = $license->resource;

        if (!$processVersion = $process->version($version, true)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        if ($license->ownedByOrganisation()) {
            $createSystemRoute = route('organisation.settings.system.create', $process->author);
        }
        else {
            $createSystemRoute = route('settings.system.create');
        }

        return view('licenses.sync', [
            'createSystemRoute' => $createSystemRoute,
            'license' => $license,
            'processVersion' => $processVersion,
            'systems' => $license->owner->systems()->with('synchronizations')->get(),
        ]);
    }

    /**
     * Eine Synchronisatin ausführen.
     * @param ExecuteLicenseSync $request
     * @param License $license
     * @param string $version
     * @return Application|RedirectResponse|Redirector
     */
    public function executeSync(ExecuteLicenseSync $request, License $license, string $version) {
        $systems = System::findMany($request->validated()['system_ids'] ?? []);

        /* @var Syncable $syncableItem */
        if (!$syncableItem = $license->resource->publishedVersions($version)) {
            return redirect(route('index'))->with('error', 'Resource existiert nicht.');
        }

        if (!$syncableItem instanceof Syncable) {
            return back()->with('info', 'Aktuell nicht möglich.');
        }

        if (!$syncableItem->definitionExportFileExists()) {
            $syncableItem->exportDefinition();
        }

        $synchronizations = $syncableItem->syncTo($systems, $license);

        $successes = $synchronizations->where('response_code', '=', Response::HTTP_OK);
        $failures = $synchronizations->where('response_code', '!=', Response::HTTP_OK);

        if ($successes->isNotEmpty()) {
            return redirect(route('license.versions', $license))->with('success', 'Erfolgreich exportiert nach: ' . $successes->pluck('system')
                        ->pluck('name')
                        ->join(', '));
        }
        if ($failures->isNotEmpty()) {
            redirect(route('license.sync', [
                'license' => $license,
                'version' => $version
            ]))->with('error', 'Fehler bei: ' . $failures->pluck('system')->pluck('name')->join(', '));
        }

        return redirect(route('license.sync', ['license' => $license, 'version' => $version]));
    }

}
