<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteDemo;
use App\Http\Requests\StoreSimulation;
use App\Loaders\PipeLoader;
use App\Models\Environment;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\User;
use App\ReferrerRedirector;
use App\SimulationConnector;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Class SimulationsController
 * @package App\Http\Controllers
 */
class SimulationsController extends Controller {

    /**
     * Anzeigen einer laufenden Simulation
     * @param Simulation $simulation
     * @return Application|ResponseFactory|RedirectResponse|Response|Redirector
     * @throws GuzzleException
     */
    public function show(Simulation $simulation) {
        if ($simulation->isFinished()) {
            return redirect()->to(request('ref') ?? route('index'))->with('info', 'Die Simulation existiert nicht mehr.');
        }

        // Benutzer und Magic-Link zur Simulation.
        try {
            $connector = new SimulationConnector($simulation);
            $users = $connector->getUsers();
            $magicLink = $connector->magicLink($simulation);
        }
        catch (Throwable $exception) {
            report($exception);

            return redirect(route('index'))->with('error', 'Leider kann die Anfrage aktuell nicht verarbeitet werden.');
        }

        // Check if the user has read access.
        if ($simulation->allisa_id) {
            try {
                $connector = new SimulationConnector($simulation);
                $connector->getProcess($simulation->allisa_id);
            }
            catch (ClientException $exception) {
                if ($exception->getCode() === Response::HTTP_FORBIDDEN) {
                    $flashWarning = 'Der gewählte Benutzer hat keinen Lese-Zugriff auf die Demo Prozess-Instanz. Wechseln Sie oben im den Benutzer oder passen Sie die Benutzer-Berechtigung des Prozesses an.';
                }
            }
        }

        /* @var User $user */
        $user = auth()->user();
        // If the origin of the simulation (started by user or organization) matches the author of the process
        // and the user is allowed to edit the process and the version has not been published.
        $displayConfigLink = $simulation->origin()->id === $simulation->process->author_id && $user->can('update', $simulation->process);
        $displayDevelopLink = $simulation->origin()->id === $simulation->process->author_id && $user->can('update', $simulation->process);

        return response(view('simulations.show', [
            'process' => $simulation->process,
            'simulation' => $simulation,
            'magicLink' => $magicLink,
            'version' => $simulation->processVersion->version,
            'users' => $users,
            'flashWarning' => $flashWarning ?? null,
            'definition' => $simulation->processVersion->definition,
            'blueprint' => $simulation->environment?->blueprint,
            'displayConfigLink' => $displayConfigLink,
            'displayDevelopLink' => $displayDevelopLink,
            'title' => $simulation->process->full_namespace . ' - Demo'
        ]));
    }

    /**
     * Simulation erstellen
     * @param StoreSimulation $request
     * @param ProcessVersion $processVersion
     * @return RedirectResponse
     * @throws Exception
     * @throws GuzzleException
     */
    public function store(StoreSimulation $request, ProcessVersion $processVersion) {
        // Create simulation. The simulation id is taken as the environment name in the Allisa platform,
        // e.g. https://322d41a9-058b-4742-b36e-86ff7c09c641.example.com
        $validated = $request->validated();
        $role = $processVersion->definition->role($validated['role_id'] ?? '') ?? $processVersion->definition->defaultRole ?? $processVersion->definition->publicRole;

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

        // Export process version and dependencies to that the most current version is imported to Allisa platform.
        if ($processVersion->isInDevelopment()) {
            $processVersion->exportDefinition();
            $processVersion->exportDependencies();
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
        catch (Exception $exception) {
            report($exception);

            if (isset($connector->token)) {
                $connector->deleteAllisaSimulation();
            }

            // Delete exported process version for simulation, e.g. allisa_demo@b6a3ebbb-2780-4317-956d-307bbdda7dda.json
            Storage::delete($simulation->definitionExportFilePath());

            // Delete exported process graph for simulation, e.g. allisa_demo@b6a3ebbb-2780-4317-956d-307bbdda7dda_graph.json
            Storage::delete($simulation->graphExportFilePath());

            $simulation->delete();

            return back()->with('error', 'Leider kann die Anfrage aktuell nicht verarbeitet werden.');
        }

        return redirect(route('simulation.show', ['simulation' => $simulation]) . '?ref=' . $validated['ref'] ?? '');
    }

    /**
     * Eine laufende Demo beenden.
     * @param DeleteDemo $request
     * @param Simulation $simulation
     * @return Application|RedirectResponse|Redirector
     */
    public function delete(DeleteDemo $request, Simulation $simulation) {
        /* @var Process $process */
        $process = $simulation->process()->first();

        if (!$process?->version($simulation->processVersion->version)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        if ($simulation->isRunning()) {
            $http = new SimulationConnector($simulation);

            try {
                // Bei Allisa die Umgebung und DB löschen
                $http->deleteAllisaSimulation();

                // Als beendet markieren und exportierten Prozess und Graphen löschen.
                $simulation->markAsFinished();
                Storage::delete($simulation->definitionExportFilePath());
                Storage::delete($simulation->graphExportFilePath());
            }
            catch (Throwable $exception) {
                report($exception);
                Log::warning('Prozess-Demo konnte nicht beendet werden: ' . $simulation->id);
            }
        }

        $ref = $request->validated()['ref'] ?? '';

        // Simulation vom Benutzer oder von einer Organisation gestartet.
        $simulationOrgigin = $simulation->organisation ?? $simulation->user;

        return redirect(ReferrerRedirector::to(base64_decode($ref), $simulationOrgigin->profileProcessesPath()))->with('success', 'Prozess-Demo beendet');
    }
}
