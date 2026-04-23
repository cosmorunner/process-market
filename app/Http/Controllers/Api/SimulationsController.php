<?php

namespace App\Http\Controllers\Api;

use App\Environment\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteAction;
use App\Http\Requests\StoreSimulation;
use App\Http\Requests\SwitchSimulationUser;
use App\Http\Resources\DefinitionAndElements as DefinitionResource;
use App\Http\Resources\Simulation as SimulationResource;
use App\Models\Environment;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\SimulationConnector;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class SimulationsController
 * @package App\Http\Controllers\Api
 */
class SimulationsController extends Controller {

    /**
     * Simulation anlegen.
     * @param StoreSimulation $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     * @throws Exception
     * @throws GuzzleException
     */
    public function store(StoreSimulation $request, ProcessVersion $processVersion) {
        // Prüfen ob der Benutzer bereits eine laufende Simulation hat.
        if ($simulation = $processVersion->runningUserSimulation()) {
            $connector = new SimulationConnector($simulation);

            try {
                $process = $connector->getProcess((string) $simulation->allisa_id);
            }
            catch (BadResponseException $exception) {
                return $connector->destroySimulation($exception);
            }

            return response()->json((new SimulationResource($simulation))->additional(['process' => $process]), Response::HTTP_OK);
        }

        // Export process version and dependencies to that the most current version is imported to Allisa platform.
        if ($processVersion->isInDevelopment()) {
            $processVersion->exportDefinition();
            $processVersion->exportDependencies();
        }

        // Simulation erstellen. Die Id wird als Environment-Name in der
        $validated = $request->validated();

        $environmentId = $validated['environment_id'] ?? null;
        $environment = $environmentId ? Environment::find($environmentId) : null;

        // Maybe, the simulation was started in the context of an organization.
        $organisationId = $validated['organisation_id'] ?? null;
        $licenseId = $validated['license_id'] ?? null;

        $role = $processVersion->definition->role($validated['role_id']) ?? $processVersion->definition->defaultRole ?? $processVersion->definition->publicRole;

        $simulation = Simulation::createForProcessVersion($processVersion, $organisationId, $licenseId);
        $connector = new SimulationConnector($simulation);

        $userEmail = $validated['user_email'] ?? 'demo@example.com';
        try {
            // Hier wird die Initialaktion ausgeführt
            $connector->instantiateSimulation($userEmail, $role, $validated['action_data'], $environment, true);
            $process = $connector->getProcess($simulation->allisa_id);
        }
        catch (Throwable $exception) {
            report($exception);

            return $connector->destroySimulation($exception);
        }

        return response()->json((new SimulationResource($simulation))->additional(['process' => $process]), Response::HTTP_CREATED);
    }

    /**
     * Beendet eine Simulation indem sie entsprechend markiert wird.
     * @param Simulation $simulation
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function finish(Simulation $simulation) {
        if ($simulation->isFinished()) {
            // Prüfen ob bereits andere Simulation in einem anderen Tab gestartet wurde.
            if (($runningSimulation = $simulation->processVersion->runningUserSimulation()) instanceof Simulation) {
                $connector = new SimulationConnector($runningSimulation);

                try {
                    $process = $connector->getProcess($runningSimulation->allisa_id);
                }
                catch (BadResponseException $exception) {
                    return $connector->destroySimulation($exception);
                }

                return response()->json((new SimulationResource($runningSimulation))->additional(['process' => $process]), Response::HTTP_OK);
            }
            // ...andernfalls die Definition zurückgeben.
            else {
                return response()->json(new DefinitionResource($simulation->processVersion));
            }
        }

        // Exportierten Prozesstyp und Graphen löschen.
        Storage::delete($simulation->definitionExportFilePath());
        Storage::delete($simulation->graphExportFilePath());

        $simulation->markAsFinished();

        try {
            $connector = new SimulationConnector($simulation);
            $connector->deleteAllisaSimulation();
        }
        catch (BadResponseException) {
            // Ignorieren, hauptsache Simulation in der Prozessfabrik wurde beendet.
        }

        // Aktuelle Definition zurückgeben da es ggfls. bereits Änderungen gibt.
        return response()->json(new DefinitionResource($simulation->processVersion));
    }

    /**
     * Als einen anderen Benutzer einloggen.
     * @param SwitchSimulationUser $request
     * @param Simulation $simulation
     * @return JsonResponse
     */
    public function switchUser(SwitchSimulationUser $request, Simulation $simulation) {
        $userId = $request->validated()['user_id'];
        $connector = new SimulationConnector($simulation);

        if ($userId === $simulation->allisa_user_id) {
            return response()->json($simulation->asResource());
        }

        $email = null;

        if ($userId === config('allisa.simulation.user_id')) {
            $email = config('allisa.simulation.user_email');
        }
        /* @var User $user */
        else if ($user = $simulation->environment->blueprint->users->firstWhere('id', '=', $userId)) {
            $email = $user->email;
        }

        try {
            $token = $connector->generateAccessToken($email, config('allisa.simulation.user_password'));
        }
        catch (Throwable $exception) {
            report($exception);

            return response()->json($simulation->asResource());
        }

        $simulation->update([
            'token' => $token,
            'allisa_user_id' => $userId
        ]);

        $simulation->refresh();

        return response()->json($simulation->asResource());
    }

    /**
     * Aktion in Simulation ausführen
     * @param ExecuteAction $request
     * @param Simulation $simulation
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function execute(ExecuteAction $request, Simulation $simulation) {
        $actionTypeId = $request->validated()['action_type_id'];
        $data = $request->validated()['data'];
        $connector = new SimulationConnector($simulation);

        try {
            $action = $connector->executeAction($simulation->allisa_id, $actionTypeId, $data);
            $process = $connector->getProcess($simulation->allisa_id);
        }
        catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $responseBody = json_decode((string) $response->getBody(), true) ?? [];

            return response()->json($responseBody, $response->getStatusCode());
        }

        return response()->json((new SimulationResource($simulation))->additional([
            'process' => $process,
            'action' => $action
        ]));
    }

    /**
     * Aktion in Simulation rückgängig machen.
     * @param Simulation $simulation
     * @param string $actionId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function undo(Simulation $simulation, string $actionId) {
        $connector = new SimulationConnector($simulation);

        try {
            $connector->revertAction($actionId);
        }
        catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $responseBody = json_decode((string) $response->getBody(), true) ?? [];

            return response()->json($responseBody, $response->getStatusCode());
        }

        return response()->json($simulation->asResource());
    }

    /**
     * Gibt den Aktionstyp inklusive der Input-Werte von der Allisa Anwendung zurück.
     * @param Simulation $simulation
     * @param string $actionTypeId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function actiontype(Simulation $simulation, string $actionTypeId) {
        $connector = new SimulationConnector($simulation);

        try {
            $actionType = $connector->getActionType($simulation->allisa_id, $actionTypeId);
        }
        catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $responseBody = json_decode((string) $response->getBody(), true) ?? [];

            return response()->json($responseBody, $response->getStatusCode());
        }

        return response()->json($actionType);
    }

    /**
     * Gibt eine Systemliste zurück.
     * @param Simulation $simulation
     * @param string $slug
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function list(Simulation $simulation, string $slug) {
        $connector = new SimulationConnector($simulation);

        try {
            $list = $connector->getList($slug);
        }
        catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $responseBody = json_decode((string) $response->getBody(), true);

            return response()->json($responseBody, $response->getStatusCode());
        }

        // Eigene Simulation nicht anzeigen
        $list['data'] = array_values(array_filter($list['data'], function ($item) use ($simulation) {
            return $item['processes_id'] !== $simulation->allisa_id;
        }));

        return response()->json($list);
    }

    /**
     * Gibt die Allisa Prozess Instanz der Simulation zurück.
     * @param Simulation $simulation
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function show(Simulation $simulation) {
        try {
            $connector = new SimulationConnector($simulation);

            // Falls die Allisa Id noch nicht existiert (z.B. weil die Simulation grade bei der Initialaktion ist.)
            // wird hier versucht diese zu laden.
            if (!$simulation->allisa_id) {
                $process = $connector->getProcess(config('allisa.simulation.process_id'));

                if ($process) {
                    $simulation->update(['allisa_id' => config('allisa.simulation.process_id')]);
                    $simulation->refresh();
                }
            }

        }
        catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $responseBody = json_decode((string) $response->getBody(), true);

            return response()->json($responseBody, $response->getStatusCode());
        }

        return response()->json(($simulation->asResource()));
    }

    /**
     * Gibt alle Benutzer der Allisa Plattform Instanz zurück in dem die REST API Route /users aufgerufen wird.
     * @param Simulation $simulation
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function users(Simulation $simulation) {
        try {
            $connector = new SimulationConnector($simulation);
            $users = $connector->getUsers();

        }
        catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $responseBody = json_decode((string) $response->getBody(), true);

            return response()->json($responseBody, $response->getStatusCode());
        }

        return response()->json($users);
    }

    /**
     * Prüft, ob es bereits eine Demo Prozess-Instanz in der Simulation gibt und
     * aktualisiert das Simulation-Model. Die Allisa-Id ist leer wenn sich die Simulation
     * noch in der Initialaktion befindet.
     * @param Simulation $simulation
     * @return JsonResponse
     */
    public function syncAllisaId(Simulation $simulation) {
        try {
            $connector = new SimulationConnector($simulation);
            $process = $connector->getProcess(config('allisa.simulation.process_id', Str::uuid()->toString()));
            $processId = $process['id'] ?? null;
            $simulation->update(['allisa_id' => $processId]);

            return response()->json(['allisa_id' => $processId]);

        }
        catch (BadResponseException|GuzzleException) {
            return response()->json(['allisa_id' => null]);
        }
    }


}
