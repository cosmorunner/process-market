<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App;

use App\Enums\Settings;
use App\Models\Setting;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Regelt das Erstellen von Allisa Plattform Demos.
 * Class ConsoleConnector
 * @package App
 */
class ConsoleConnector {

    /**
     * @var Client
     */
    private $http;

    /**
     * JWT-Token
     * @var string
     */
    public $token = null;

    /**
     * Name der zuletzt ausgeführten Methode.
     * @var string
     */
    public $lastMethod;

    /**
     * Allisa Console REST-API Endpunkt.
     * @var string
     */
    public $allisaConsoleApiEndpoint;

    public function __construct() {
        $this->http = app(Client::class);
        $this->allisaConsoleApiEndpoint = config('allisa.console.api_endpoint');
        $this->token = Setting::retrieve(Settings::AllisaConsoleToken->value);

        if ($this->shouldUpdateToken()) {
            $this->updateAccessToken();
        }
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
     * Gibt die Standard Optionen für eine neue Demo-Plattform zurück.
     * @return array
     */
    private function defaultPlatformOptions(): array {
        return [
            'name' => 'Allisa Plattform',
            'description' => 'Digitalisierungsplattform für Geschäftsprozesse und Regelwerke.',
            'locale' => 'de',
            'email' => Auth::user()->email,
            'blueprint' => 'demo',
            'license' => 'demo',
            'create_market_client' => '1'
        ];
    }

    /**
     * Erstellt einen JWT Token und speichert diesen in den Einstellungen ab.
     * @return void
     */
    public function updateAccessToken() {
        $this->lastMethod = __FUNCTION__;

        try {
            $response = $this->http->post(config('allisa.console.oauth_endpoint'), [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'read-instances create-instances',
                    'client_id' => config('allisa.console.client_id', ''),
                    'client_secret' => config('allisa.console.client_secret', ''),
                ]
            ]);

            $body = json_decode((string) $response->getBody(), true) ?? [];

            // Save in settings
            if (array_key_exists('expires_in', $body) && array_key_exists('access_token', $body)) {
                $expiresAt = Carbon::now()->addSeconds($body['expires_in'])->toDateTimeString();
                $accessToken = $body['access_token'];

                Setting::upsertSetting(Settings::AllisaConsoleTokenExpiresAt->value, $expiresAt);
                Setting::upsertSetting(Settings::AllisaConsoleToken->value, $accessToken);

                $this->token = $accessToken;
            }
        }
        catch (ClientException|RequestException|GuzzleException $exception) {
            report($exception);
        }
    }

    /**
     * If the Allisa Console Token does not exist in the DB or has expired, a new token must be generated.
     * @return bool
     */
    public function shouldUpdateToken(): bool {
        $token = Setting::retrieveSystem(Settings::AllisaConsoleToken->value);
        $expiresAt = Setting::retrieveSystem(Settings::AllisaConsoleTokenExpiresAt->value, Carbon::yesterday());
        $date = Carbon::createFromTimeString($expiresAt);

        return (!$token || $date->isPast());
    }

    /**
     * Erzeugt die Simulations-Umgebung mit SQLite DB und eigener .env-Datei.
     * @param array $options
     * @return array
     * @throws GuzzleException
     */
    public function createPlatform(array $options = []): array {
        $this->lastMethod = __FUNCTION__;

        $options = array_merge($this->defaultPlatformOptions(), $options);

        $response = $this->http->post(config('allisa.console.api_endpoint') . '/instances', [
            'headers' => $this->defaultHeaders(),
            'json' => $options,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Prüft, ob es bereits eine Allisa Demo Plattform mit dem Identifier gibt.
     * @param string $identifier
     * @return bool
     * @throws GuzzleException
     */
    public function platformExists(string $identifier): bool {
        $this->lastMethod = __FUNCTION__;

        try {
            $response = $this->http->get($this->allisaConsoleApiEndpoint . '/instances/' . $identifier, [
                'headers' => $this->defaultHeaders(),
            ]);
        }
        catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return false;
            }

            throw $exception;
        }

        return $response->getStatusCode() === Response::HTTP_OK;
    }

}
