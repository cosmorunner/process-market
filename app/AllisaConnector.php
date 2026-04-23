<?php

/** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App;

use App\Enums\Settings;
use App\Models\Setting;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Connector to the Allisa plattform that is used for creating demos and simulations.
 * Class AllisaConnector
 * @package App
 */
class AllisaConnector {

    /**
     * @var Client
     */
    private $http;

    /**
     * JWT-Token
     * @var string
     */
    public $token;

    /**
     * Name of the last called method.
     * @var string
     */
    public $lastMethod;

    /**
     * Allisa REST-API endpoint.
     * @var string
     */
    public $apiEndpoint;

    /**
     * Allisa OAuth endpoint.
     * @var string
     */
    public $oauthEndpoint;


    public function __construct() {
        $this->http = app(Client::class);
        $this->apiEndpoint = config('allisa.api_endpoint');
        $this->oauthEndpoint = config('allisa.oauth_endpoint');
        $this->token = Setting::retrieve(Settings::AllisaToken->value);

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
     * Creates a new access token in the Allisa Plattform.
     * @return string
     */
    public function updateAccessToken() {
        $this->lastMethod = __FUNCTION__;

        try {
            $response = $this->http->post($this->oauthEndpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'grant_type' => 'password',
                    'client_id' => config('allisa.password_client_id'),
                    'client_secret' => config('allisa.password_client_secret'),
                    'username' => config('allisa.admin_email'),
                    'password' => config('allisa.admin_password'),
                    'scope' => ''
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true) ?? [];

            // Save in settings
            if (array_key_exists('expires_in', $body) && array_key_exists('access_token', $body)) {
                $expiresAt = Carbon::now()->addSeconds($body['expires_in'])->toDateTimeString();
                $accessToken = $body['access_token'];

                Setting::upsertSetting(Settings::AllisaTokenExpiresAt->value, $expiresAt);
                Setting::upsertSetting(Settings::AllisaToken->value, $accessToken);

                $this->token = $accessToken;
            }
        }
        catch (ClientException|RequestException|GuzzleException $exception) {
            report($exception);
        }

        return $this->token;
    }

    /**
     * If the Allisa Console Token does not exist in the DB or has expired, a new token must be generated.
     * @return bool
     */
    public function shouldUpdateToken(): bool {
        $token = Setting::retrieveSystem(Settings::AllisaToken->value);
        $expiresAt = Setting::retrieveSystem(Settings::AllisaTokenExpiresAt->value, Carbon::yesterday());
        $date = Carbon::createFromTimeString($expiresAt);

        // In development, we always update token as the stored token is probably invalid due to blueprint:run initializations.
        return (env('APP_ENV') === 'local' || !$token || $date->isPast());
    }

    /**
     * @param string $template Base64 encoded template string
     * @param string $type Type of template, "custom_logic" or "html".
     * @param array $data Data with which the template should be rendered.
     * @param array $options Rendering options
     * @return JsonResponse|Response
     */
    public function previewTemplate(string $template, string $type, array $data, array $options = []) {
        $this->lastMethod = __FUNCTION__;

        try {
            return $this->http->post($this->apiEndpoint . '/utils/preview-template', [
                'headers' => $this->defaultHeaders(),
                'json' => [
                    'template' => $template,
                    'type' => $type,
                    'data' => $data,
                    'options' => $options
                ]
            ]);
        }
        catch (ClientException|ServerException $exception) {
            $body = json_decode((string) $exception->getResponse()->getBody(), true) ?? [];

            return response()->json($body, Response::HTTP_BAD_REQUEST);
        }

    }

}
