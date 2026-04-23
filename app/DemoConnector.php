<?php

/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace App;

use App\Models\Demo;
use App\Models\ProcessVersion;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

/**
 * Regelt das Erstellen von Demos.
 * Class DemoConnector
 * @package App
 */
class DemoConnector {

    /**
     * @var Client
     */
    private $http;

    /**
     * Name der Allisa-Umgebung
     * @var string
     */
    public $env;

    /**
     * JWT-Token
     * @var string
     */
    public $token;

    /**
     * Name der zuletzt ausgeführten Methode.
     * @var string
     */
    public $lastMethod;

    /**
     * Allisa REST-API Endpunkt.
     * @var string
     */
    public $liveDemoEndpoint;

    /**
     * Allisa Web Endpunkt.
     * @var string
     */
    public $liveDemoHost;

    /**
     * @var Demo
     */
    private $demo;

    public function __construct(Demo $demo) {
        $this->demo = $demo;
        $this->env = $demo->id;
        $this->token = $demo->token;
        $this->http = app(Client::class);
        $this->liveDemoEndpoint = sprintf(config('allisa.live_demo.api_endpoint'), $this->env);
        $this->liveDemoHost = sprintf(config('allisa.live_demo.host'), $this->env);
    }

    /**
     * Gibt die Standard-Header zurück.
     * @return array
     */
    #[ArrayShape(['Accept' => "string", 'Authorization' => "string"])]
    private function defaultHeaders() {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
    }

    /**
     * Erzeugt die Simulations-Umgebung mit SQLite DB und eigener .env-Datei.
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function createEnvironment() {
        $this->lastMethod = __FUNCTION__;

        return $this->http->post(config('allisa.api_endpoint') . '/simulations', [
            'json' => [
                'name' => $this->env,
                'blueprint_name' => 'live-demo',
                'secret' => config('allisa.live_demo.secret')
            ],
        ]);
    }

    /**
     * Aktualisiert die Simulations-Umgebung, indem eine Blueprint-Konfiguration ausgeführt wird.
     * @param array $blueprint
     * @param array $fileNames
     * @return ResponseInterface
     * @throws GuzzleException
     * @noinspection PhpUnused
     */
    public function patchEnvironment(array $blueprint, array $fileNames = []) {
        $this->lastMethod = __FUNCTION__;

        $parts = [
            // Notwendiger helper um bei einem PATCH Request Dateien mitsenden zu können.
            [
                'name' => '_method',
                'contents' => 'PATCH'
            ],
            [
                'name' => 'blueprint',
                'contents' => json_encode($blueprint)
            ],
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

        return $this->http->post($this->liveDemoEndpoint . '/simulations/' . $this->env, [
            'headers' => $this->defaultHeaders(),
            'multipart' => $parts
        ]);
    }

    /**
     * Löscht eine Simulations-Umgebung bei der Allisa-Anwendung.
     * @throws GuzzleException
     */
    public function deleteAllisaLiveDemo() {
        if (!$this->token) {
            return;
        }

        $this->http->delete(config('allisa.api_endpoint') . '/simulations/' . $this->env, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'blueprint_name' => 'live-demo',
                'secret' => config('allisa.live_demo.secret')
            ],
        ]);
    }


    /**
     * Erzeugt einen neuen Access-Token bei der Allisa-Anwendung.
     * @param $email
     * @param $password
     * @return string
     * @throws GuzzleException
     */
    public function generateAccessToken($email = null, $password = null) {
        $this->lastMethod = __FUNCTION__;

        $userEmail = $this->demo->main ? config('allisa.live_demo.admin_email') : config('allisa.live_demo.user_email');
        $userPassword = $this->demo->main ? config('allisa.live_demo.admin_password') : config('allisa.live_demo.user_password');

        $response = $this->http->post($this->liveDemoHost . '/oauth/token', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('allisa.live_demo.password_client_id'),
                'client_secret' => config('allisa.live_demo.password_client_secret'),
                'username' => $email ?? $userEmail,
                'password' => $password ?? $userPassword,
                'scope' => 'sync-processtypes',
            ],
        ]);

        $this->token = json_decode((string) $response->getBody())->access_token;

        return $this->token;
    }

    /**
     * Loggt den Benutzer "demo@example.com" beim Simulation-Environment ein und gibt die
     * den gültigen Session-Cookie zurück.
     * @return string Magic Link
     * @throws GuzzleException
     */
    public function magicLink() {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->post($this->liveDemoEndpoint . '/account/magic-link', [
            'headers' => $this->defaultHeaders(),
        ]);

        return (json_decode((string) $response->getBody()))->url;
    }

    /**
     * Importiert Prozesstypen im Allisa System.
     * @param array $fullNamespacesWithVersion
     * @return ResponseInterface|null $fullNamespaceWithVersion
     * @throws GuzzleException
     */
    public function importProcessTypes(array $fullNamespacesWithVersion) {
        $this->lastMethod = __FUNCTION__;

        if (empty($fullNamespacesWithVersion)) {
            return null;
        }

        $fileNames = array_map(fn($item) => namespace_to_definition_export_file_name($item), $fullNamespacesWithVersion);

        // Berechtigung für die "Direktor" Rolle mitsenden.
        $roleId = config('allisa.live_demo.default_director_role_id');
        $createProcessIdent = config('allisa.live_demo.permission_process_create');
        $deleteProcessIdent = config('allisa.live_demo.permission_process_delete');
        $parts = [
            [
                'name' => 'permissions',
                'contents' => json_encode([$roleId => [$createProcessIdent, $deleteProcessIdent]])
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

        $response = $this->http->post($this->liveDemoEndpoint . '/processtypes', [
            'headers' => $this->defaultHeaders(),
            'multipart' => $parts
        ]);

        return json_decode((string) $response->getBody());
    }

    /**
     * Importiert Prozesstypen im Allisa System.
     * @param array $fullNamespacesWithVersion
     * @return bool|null $fullNamespaceWithVersion
     * @throws GuzzleException
     */
    public function deleteProcessTypes(array $fullNamespacesWithVersion) {
        $this->lastMethod = __FUNCTION__;

        if (empty($fullNamespacesWithVersion)) {
            return null;
        }

        foreach ($fullNamespacesWithVersion as $item) {
            $parts = namespace_parts($item);
            $namespace = $parts['namespace'];
            $identifier = $parts['identifier'];
            $version = $parts['version'] ?? 'latest';
            $url = $this->liveDemoEndpoint . '/processtypes/' . $namespace . '_' . $identifier . '/' . $version;

            $this->http->delete($url, ['headers' => $this->defaultHeaders()]);
        }

        return true;
    }

    /**
     * Erzeugt eine neue Simulation.
     * @throws GuzzleException
     * @throws Exception
     */
    public function instantiateDemo() {
        $this->createEnvironment();
        $this->generateAccessToken();

        $solutionVersion = $this->demo->solutionVersion;

        // Prozesstypen in die Simulations-Allisa-Instanz importieren.
        $allFilenames = $solutionVersion->dependentProcessFileNames()->toArray();
        $processTypes = count($allFilenames) ? $this->importProcessTypes($allFilenames) : [];

        if (!is_object($processTypes) && !is_array($processTypes)) {
            throw new Exception('Prozesstyp-Import fehlgeschlagen');
        }

        $this->demo->update(['token' => $this->token]);
    }

    /**
     * Erstellt eine Demo für den Benutzer, indem die "main" Demo kopiert wird.
     * @return string|null Name der Simulation (Id der Demo
     * @throws GuzzleException
     */
    public function copyMainDemo(): string|null {
        $response = $this->http->post(config('allisa.api_endpoint') . '/simulations/' . $this->demo->id . '/copy', [
            'json' => ['secret' => config('allisa.live_demo.secret')]
        ]);

        $body = json_decode((string) $response->getBody(), true) ?? [];

        return $body['name'] ?? null;
    }

    /**
     * Gibt alle nicht "locked" Prozesstypen der Demo aus der Allisa-Anwendung zurück.
     * @param bool $onlyFullNamespacesWithVersion
     * @return array
     * @throws GuzzleException
     */
    public function getProcessTypeMetas(bool $onlyFullNamespacesWithVersion = false) {
        $this->lastMethod = __FUNCTION__;

        $response = $this->http->get($this->liveDemoEndpoint . '/processtypes', [
            'headers' => $this->defaultHeaders(),
        ]);

        $body = json_decode((string) $response->getBody(), true) ?? [];

        if ($onlyFullNamespacesWithVersion) {
            // Die vollständigen Namespaces
            return collect($body)->filter(fn($item) => !$item['locked'])->reduce(function (Collection $carry, $item) {
                $versions = collect($item['versions'] ?? [])->map(fn($item) => $item['version']);

                return $carry->concat($versions->map(fn($version) => $item['full_namespace'] . '@' . $version));
            }, collect())->toArray();
        }

        return $body;
    }

    /**
     * Basierend auf den "process_types" Array im "data"-Attribut der Lösungsversion werden alle abhängigen Prozesse ermittelt
     * und diese importiert bzw. entfernt.
     * @return void
     * @throws ContainerExceptionInterface
     * @throws GuzzleException
     * @throws NotFoundExceptionInterface
     */
    public function syncProcessTypes() {
        $existingNamespacesInDemo = $this->getProcessTypeMetas(true);
        $toImport = [];
        $toDelete = [];

        // Erforderliche Prozesstyp-Versionen
        $graphs = $this->demo->solutionVersion->processVersions();
        $dependentProcesses = $graphs->map(fn(ProcessVersion $processVersion) => $processVersion->full_namespace);

        // Alle Namespaces aller direkt abhängigen und indirekt abhängigen Prozesse.
        /* @var Collection $graphNamespaces */
        $graphNamespaces = $graphs->reduce(fn(Collection $carry, ProcessVersion $processVersion) => $carry->merge($processVersion->processTypeDependencies()), $dependentProcesses)
            ->unique();

        // Alle "latest" Versionen mit der konkreten Version ermitteln.
        $concreteDependencies = $graphNamespaces->filter(fn(string $item) => str_ends_with($item, '@latest'))
            ->map(fn(string $item) => ProcessVersion::findByFullNamespace($item))
            ->map(fn(ProcessVersion $processVersion) => $processVersion->full_namespace);

        $graphNamespaces = $graphNamespaces->filter(fn(string $item) => !str_ends_with($item, '@latest'))
            ->merge($concreteDependencies)
            ->unique();

        // Für jeden Graphen, der gemäß Prozesstyp-Abhängigkeiten der SolutionVersion in der Main Demo sein sollte
        // wird zu "toImport" hinzugefügt, damit diese dann importiert werden können.
        foreach ($graphNamespaces as $fullNamespace) {
            if (!in_array($fullNamespace, $existingNamespacesInDemo)) {
                $toImport[] = $fullNamespace;
            }
        }

        // Alle in der Main Demo vorhandenen Prozesstyp-Versionen, die NICHT in den Prozesstyp-Abhängigkeiten
        // vorhanden sind, werden zu "toDelete" hinzugefügt, damit diese aus der Main Demo gelöscht werden.
        foreach ($existingNamespacesInDemo as $fullNamespaceWithVersion) {
            if (!$graphNamespaces->contains($fullNamespaceWithVersion)) {
                $toDelete[] = $fullNamespaceWithVersion;
            }
        }

        $this->importProcessTypes($toImport);
        $this->deleteProcessTypes($toDelete);
    }
}
