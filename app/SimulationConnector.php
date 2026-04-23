<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App;

use App\Models\Environment;
use App\Models\Simulation;
use App\ProcessType\ActionType;
use App\ProcessType\Role;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Spatie\Url\Url;
use Throwable;

/**
 * Manages the instantiation of simulations.
 * Class SimulationConnector
 * @package App
 */
class SimulationConnector {

    /**
     * @var Client
     */
    private Client $http;

    /**
     * Name der Allisa-Umgebung
     * @var string
     */
    public $env;

    /**
     * Name der zuletzt ausgeführten Methode.
     * @var string
     */
    public $lastMethod;

    /**
     * Allisa REST-API Endpunkt.
     * @var string
     */
    public $simulationApiEndpoint;

    /**
     * Allisa Web Endpunkt.
     * @var string
     */
    public $simulationHost;

    /**
     * @var Simulation
     */
    private $simulation;

    /**
     * @param Simulation $simulation
     */
    public function __construct(Simulation $simulation) {
        $this->simulation = $simulation;
        $this->env = config('allisa.simulation.environment_name', 'uuid') != 'uuid' ? 'simulation' : $simulation->id;
        $this->http = app(Client::class);
        $this->simulationApiEndpoint = sprintf(config('allisa.simulation.api_endpoint'), $this->env);
        $this->simulationHost = sprintf(config('allisa.simulation.host'), $this->env);
    }

    /**
     * Gibt die Standard-Header zurück.
     * @return array
     */
    #[ArrayShape(['Accept' => "string", 'Authorization' => "string"])]
    private function defaultHeaders() {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->simulation->token
        ];
    }

    /**
     * Creates the simulation environment with SQLite DB and own .env file.
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function createAllisaSimulation() {
        $this->lastMethod = __FUNCTION__;

        return $this->http->post(config('allisa.api_endpoint') . '/simulations', [
            'json' => [
                'name' => $this->env,
                'blueprint_name' => 'simulation',
                'secret' => config('allisa.simulation.secret')
            ],
        ]);
    }

    /**
     * Updates the simulation environment by running a blueprint configuration.
     * @param Environment|null $environment
     * @return ResponseInterface|void
     * @throws GuzzleException
     */
    public function patchEnvironment(Environment $environment = null) {
        $this->lastMethod = __FUNCTION__;

        if (!$environment) {
            return;
        }

        return $this->http->post($this->simulationApiEndpoint . '/simulations/' . $this->env, [
            'headers' => $this->defaultHeaders(),
            'multipart' => [
                // Notwendiger helper um bei einem PATCH Request Dateien mitsenden zu können.
                [
                    'name' => '_method',
                    'contents' => 'PATCH'
                ],
                [
                    'name' => 'blueprint',
                    'contents' => json_encode($environment->getRawBlueprint())
                ],
            ]
        ]);
    }

    /**
     * Deletes a simulation environment in the Allisa platform.
     * @throws GuzzleException
     */
    public function deleteAllisaSimulation() {
        if (!$this->simulation->token) {
            return;
        }

        $this->http->delete(config('allisa.api_endpoint') . '/simulations/' . $this->env, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'blueprint_name' => 'simulation',
                'secret' => config('allisa.simulation.secret')
            ],
        ]);
    }


    /**
     * Creates a new access token at the Allisa platform.
     * @param $email
     * @param $password
     * @return string
     * @throws GuzzleException
     */
    public function generateAccessToken($email = null, $password = null) {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->post($this->simulationHost . '/oauth/token', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('allisa.simulation.password_client_id'),
                'client_secret' => config('allisa.simulation.password_client_secret'),
                'username' => $email ?? config('allisa.simulation.user_email'),
                'password' => $password ?? config('allisa.simulation.user_password'),
                'scope' => 'sync-processtypes',
            ],
        ]);

        $token = json_decode((string) $response->getBody())->access_token;
        $this->simulation->update(['token' => $token]);

        return $token;
    }

    /**
     * Logs the user "demo@example.com" into the simulation environment and returns the
     * returns the valid session cookie.
     * @param Simulation $simulation
     * @return string Magic Link
     * @throws GuzzleException
     */
    public function magicLink(Simulation $simulation) {
        $this->lastMethod = __FUNCTION__;
        $onCreateActionType = $simulation->environment?->initialActionType();
        $defaultAllisaId = config('allisa.simulation.process_id');

        // Sollte bei der Simulation die Prozess-Instanz bereits angelegt worden sein, kann direkt zur Prozess-Instanz
        // weitergeleitet werden.
        if ($simulation->allisa_id) {
            $redirect = $this->simulationHost . '/processes/' . $simulation->allisa_id;
        }
        // Andernfalls wird auf die Initialaktion weitergeleitet
        else {
            // Direkte Prüfung auf Simulations-DB
            try {
                $process = $this->getProcess($defaultAllisaId);
            }
            catch (ClientException) {
                $process = null;
            }

            // Falls noch keine Prozess-Instanz existiert wird hier der Link zur Initialaktion gesetzt.
            if (!$process && $onCreateActionType instanceof ActionType) {
                // Namespace für URL umformatieren
                $fullNamespace = str_replace('/', '_', $simulation->process->full_namespace);
                $url = Url::fromString($this->simulationHost . '/processtypes/' . $fullNamespace . '/' . $simulation->processVersion->version . '/start/' . $onCreateActionType->id)
                    ->withQueryParameter('id', $defaultAllisaId);

                // Gegebenenfalls den "context" Query Parameter hinzufügen
                if ($simulation->context) {
                    $url = $url->withQueryParameter('context', $simulation->context);
                }

                $redirect = (string) $url;
            }
            // Ansonsten wird die Allisa-Id bei der Simulation hinterlegt und zur Prozess-Instanz weitergeleitet.
            else {
                $simulation->update(['allisa_id' => $defaultAllisaId]);
                $redirect = $this->simulationHost . '/processes/' . $defaultAllisaId;
            }
        }

        $response = $this->http->post($this->simulationApiEndpoint . '/account/magic-link', [
            'headers' => $this->defaultHeaders(),
            'json' => [
                'redirect' => $redirect
            ],
        ]);

        return (json_decode((string) $response->getBody()))->url;
    }

    /**
     * Imports process types in Allisa platform.
     * @param Environment|null $environment
     * @return ResponseInterface|null $fullNamespaceWithVersion
     * @throws GuzzleException
     * @throws Exception
     */
    public function importProcessTypes(Environment $environment = null) {
        $this->lastMethod = __FUNCTION__;

        $processVersion = $this->simulation->processVersion;

        // Collect all filenames of dependent process versions.
        $dependencyFileNames = $processVersion->dependentProcessFileNames();

        // Process filenames from environment.
        $environmentFileNames = $environment ? $environment->exportProcesses($processVersion->full_namespace) : collect();

        // Export dependent processes to JSON files.

        // File names of the process definitions
        $fileNames = collect([$processVersion->definitionExportFileName()])
            ->concat($dependencyFileNames->toArray())
            ->concat($environmentFileNames->toArray())
            ->unique()
            ->toArray();

        if (empty($fileNames)) {
            return null;
        }

        // Send permissions for the "Director" role.
        $directorGroupRoleId = config('allisa.simulation.default_director_role_id');
        $userSystemRoleId = config('allisa.simulation.default_system_role_id');
        $createProcessIdent = config('allisa.simulation.permission_process_create');
        $deleteProcessIdent = config('allisa.simulation.permission_process_delete');
        $parts = [
            [
                'name' => 'permissions',
                'contents' => json_encode([
                    $userSystemRoleId => [$createProcessIdent, $deleteProcessIdent],
                    $directorGroupRoleId => [$createProcessIdent, $deleteProcessIdent]
                ])
            ]
        ];

        foreach ($fileNames as $fileName) {
            $path = config('app.process_types_dir') . '/' . $fileName;

            if (Storage::exists($path)) {
                $parts[] = [
                    'name' => 'process_types[]',
                    'contents' => Storage::get($path),
                    'filename' => Uuid::uuid4()->toString()
                ];
            }
        }

        $response = $this->http->post($this->simulationApiEndpoint . '/processtypes', [
            'headers' => $this->defaultHeaders(),
            'multipart' => $parts
        ]);

        $body = json_decode((string) $response->getBody());

        if (!is_object($body) && !is_array($body)) {
            throw new Exception('Prozesstyp-Import fehlgeschlagen.');
        }

        return $body;
    }

    /**
     * Creates the demo process instance in the Allisa simulation.
     * @param string $fullNamespaceWithVersion
     * @param string|null $roleId
     * @param array $actionData
     * @return array
     * @throws GuzzleException
     */
    public function createProcess(string $fullNamespaceWithVersion, $roleId = null, array $actionData = []) {
        $this->lastMethod = __FUNCTION__;
        $mappedActionData = [];

        foreach ($actionData as $name => $value) {
            $mappedActionData[] = [
                'name' => $name,
                'contents' => $value
            ];
        }

        $accesses = $roleId ? [
            'name' => 'accesses',
            'contents' => json_encode(['self' => $roleId])
        ] : null;

        $parts = [
            [
                'name' => 'id',
                'contents' => config('allisa.simulation.process_id')
            ],
            [
                'name' => 'name',
                'contents' => 'Demo-Instanz'
            ],
            [
                'name' => 'process_type',
                'contents' => $fullNamespaceWithVersion
            ],
            ...$mappedActionData
        ];

        if ($accesses) {
            $parts = [...$parts, $accesses];
        }

        $response = $this->http->post($this->simulationApiEndpoint . '/processes', [
            'headers' => $this->defaultHeaders(),
            'multipart' => $parts
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }

    /**
     * Returns the process of simulation from the Allisa simulation.
     * @param string $processId
     * @return array
     * @throws GuzzleException
     */
    public function getProcess(string $processId) {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->get($this->simulationApiEndpoint . '/processes/' . $processId, [
            'headers' => $this->defaultHeaders(),
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }

    /**
     * Returns all users of the simulation from the Allisa simulation.
     * @return array
     * @throws GuzzleException
     */
    public function getUsers() {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->get($this->simulationApiEndpoint . '/users', [
            'headers' => $this->defaultHeaders(),
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }

    /**
     * Returns an action type including the input values back from the Allisa simulation.
     * @param string $processId
     * @param string $actionTypeId
     * @return array
     * @throws GuzzleException
     */
    public function getActionType(string $processId, string $actionTypeId) {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->get($this->simulationApiEndpoint . '/processes/' . $processId . '/actiontypes/' . $actionTypeId, [
            'headers' => $this->defaultHeaders(),
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }

    /**
     * Returns a system list.
     * @param string $slug
     * @param string $search
     * @return array
     * @throws GuzzleException
     */
    public function getList(string $slug, string $search = '') {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->get($this->simulationApiEndpoint . '/lists/' . $slug . '?search=' . $search, [
            'headers' => $this->defaultHeaders(),
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }

    /**
     * Perform an action while the simulation is running.
     * @param string $processId
     * @param string $actionTypeId
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public function executeAction(string $processId, string $actionTypeId, array $data) {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->post($this->simulationApiEndpoint . '/processes/' . $processId . '/actiontypes/' . $actionTypeId, [
            'headers' => $this->defaultHeaders(),
            'multipart' => $this->buildMultiparts($data)
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Execute the initial action when starting a simulation.
     * @param string $fullNamespaceWithVersion
     * @param ActionType $actionType
     * @param array $data
     * @return mixed
     * @throws GuzzleException
     */
    public function executeInitialAction(string $fullNamespaceWithVersion, ActionType $actionType, array $data) {
        $this->lastMethod = __FUNCTION__;
        $parts = namespace_parts($fullNamespaceWithVersion);
        $fullNamespace = $parts['namespace'] . '_' . $parts['identifier'];
        $version = $parts['version'];

        // The query parameter "id" is specified here, so that the demo process is
        // is created in the simulation with this id.
        $response = $this->http->post($this->simulationApiEndpoint . '/processtypes/' . $fullNamespace . '/' . $version . '/start/' . $actionType->id . '/?id=' . config('allisa.simulation.process_id'), [
            'headers' => $this->defaultHeaders(),
            'multipart' => $this->buildMultiparts($data)
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Undo an action in the Allisa simulation.
     * @param string $actionId
     * @return bool
     * @throws GuzzleException
     */
    public function revertAction(string $actionId) {
        $this->lastMethod = __FUNCTION__;

        $this->http->delete($this->simulationApiEndpoint . '/actions/' . $actionId, [
            'headers' => $this->defaultHeaders()
        ]);

        return true;
    }

    /**
     * Creates a new simulation by creating the environment, generating the access token, importing the process types and
     * patching the simulation with a blueprint.
     * @param string $executedUserEmail
     * @param Role|null $role
     * @param array $actionData
     * @param Environment|null $environment
     * @param bool $executeInitialAction
     * @throws GuzzleException
     * @throws Exception
     */
    public function instantiateSimulation(string $executedUserEmail, Role $role = null, array $actionData = [], Environment $environment = null, bool $executeInitialAction = false) {
        // Create Allisa simulation with .env file and sqlite simulation database.
        $this->createAllisaSimulation();

        // Generate a new access token from the Allisa simulation environment.
        $this->generateAccessToken();

        $processVersion = $this->simulation->processVersion;

        // Export process version as JSON so that process type changes during running simulation does not corrupt the
        // simulation display under "Rules and Data", because changes were made in the meantime.
        // e.g. allisa_demo@d3b9b9b5-6d14-4ff6-84fb-77b7963a6f95.json
        $this->simulation->exportDefinition();

        // Graph as JSON as well.
        // e.g. allisa_demo@d3b9b9b5-6d14-4ff6-84fb-77b7963a6f95_graph.json
        $this->simulation->exportGraph();

        // Import all process types. Demo process type, process types from dependencies and process types from environment.
        $this->importProcessTypes($environment);

        // If an environment is specified or a default environment exists, the simulation Allisa process instance must be patched accordingly.
        // i.e. add more users, processes links or groups.
        $this->patchEnvironment($environment);

        // We start the demo with a different user.
        if ($executedUserEmail !== 'demo@example.com') {
            $token = $this->generateAccessToken($executedUserEmail, config('allisa.simulation.user_password'));
            $userId = $environment->blueprint->users->firstWhere('email', '=', $executedUserEmail)->id;
            $this->simulation->update([
                'token' => $token,
                'allisa_user_id' => $userId
            ]);
        }

        // Environment defines initial action or not.
        $initialActionType = $environment?->initialActionType();

        // No intial action, simply create process.
        if (!$initialActionType instanceof ActionType) {
            $process = $this->createProcess($processVersion->full_namespace, $role?->id, $actionData);
        }

        // Bei Initialaktion und API-Aufruf (via /develop) versuchen diese auszuführen.
        // Bei einer Demo-Instantiierung muss zur Initialaktion im Allisa-System weitergeleitet werden.
        if ($initialActionType instanceof ActionType && $executeInitialAction) {
            $action = $this->executeInitialAction($processVersion->full_namespace, $initialActionType, $actionData);
        }

        $this->simulation->update([
            'allisa_id' => $process['id'] ?? $action['process_id'] ?? null,
            'environment_id' => $environment?->id
        ]);
    }

    /**
     * Sollte es beim Erstellen der Simulation oder beim Holen des Prozesses zu einem Fehler kommen
     * soll die Simulation vollständig entfernt werden.
     * @param Throwable $exception
     * @return JsonResponse
     */
    public function destroySimulation(Throwable $exception) {
        // Im SimulationObserver wird die Allisa-Simulation entfernt.
        $this->simulation->delete();

        // Exportierten Prozesstyp und Graphen löschen
        Storage::delete($this->simulation->definitionExportFilePath());
        Storage::delete($this->simulation->graphExportFilePath());

        // Bei einer Standard-Fehlermeldung
        if (!$exception instanceof RequestException) {
            $responseJson = ['last_method' => $this->lastMethod, 'message' => $exception->getMessage()];

            return response()->json($responseJson, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Connector-RequestException...
        $response = $exception->getResponse();
        $responseJson = ['last_method' => $this->lastMethod];
        $responseBody = json_decode((string) $response->getBody(), true);

        if (is_array($responseBody)) {
            $responseJson = array_merge($responseBody, $responseJson);
        }

        // Falls ein 403-Fehler (Nicht authorisiert) auftritt, liegt dies vermutlich daran, dass keine Standard-Rolle
        // angegeben wurde oder die Initialaktion keinen Zugriff für den Benutzer erzeugt.
        if ($response->getStatusCode() === Response::HTTP_FORBIDDEN) {
            $responseJson = [
                'message' => 'Sie haben keinen Zugriff auf die erzeugte Simulations-Instanz.
                    Stellen Sie sicher, dass eine Standard-Rolle angegeben ist oder die Initialaktion einen Zugriff erzeugt.'
            ];
        }

        return response()->json($responseJson, $response->getStatusCode());
    }

    /**
     * Erzeugt das Multi-Part Array für den ExecuteAction/ExecuteInitialAction Form-Request
     * @param array $data
     * @return array
     */
    private function buildMultiparts(array $data) {
        $items = [];

        foreach ($data as $name => $value) {
            // Wenn der Wert eine Liste und KEIN Objekt ist.
            if (is_array($value) && !Arr::isAssoc($value)) {
                foreach ($value as $index => $item) {
                    if (is_string($item) || is_null($item)) {
                        $items[] = [
                            'name' => $name . '[' . $index . ']',
                            'contents' => $item
                        ];
                    }
                    if (is_resource($item)) {
                        $items[] = [
                            'name' => $name . '[' . $index . '][0]',
                            'contents' => $item
                        ];
                    }
                }
            }
            if (is_string($value)) {
                $items[] = [
                    'name' => $name,
                    'contents' => $value
                ];
            }
            // Datei
            if (is_resource($value)) {
                $items[] = [
                    'name' => $name . '[0]',
                    'contents' => $value
                ];
            }
        }

        return $items;
    }
}
