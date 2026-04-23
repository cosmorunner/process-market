<?php

namespace App\Models;

use App\Interfaces\Syncable;
use Carbon\Carbon;
use Eloquent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Verbundenes Allisa System zu dem Prozess synchronisiert werden können.
 * Class System
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string|null $owner_id
 * @property string|null $owner_type
 * @property string $client_id
 * @property string $client_secret
 * @property string|null $token
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|Eloquent $owner
 * @property-read Collection|Synchronization[]|Eloquent $synchronizations
 * @method static Builder|System newModelQuery()
 * @method static Builder|System newQuery()
 * @method static Builder|System query()
 * @method static Builder|System whereClientId($value)
 * @method static Builder|System whereClientSecret($value)
 * @method static Builder|System whereCreatedAt($value)
 * @method static Builder|System whereExpiresAt($value)
 * @method static Builder|System whereId($value)
 * @method static Builder|System whereName($value)
 * @method static Builder|System whereOwnerId($value)
 * @method static Builder|System whereOwnerType($value)
 * @method static Builder|System whereToken($value)
 * @method static Builder|System whereUpdatedAt($value)
 * @method static Builder|System whereUrl($value)
 * @method static Builder|System create($value)
 * @mixin Eloquent
 * @property-read int|null $synchronizations_count
 */
class System extends Model {

    use HasUuids, HasFactory;

    protected $guarded = [];

    /**
     * Benutzer oder Organisation, welche dieses Allisa System verbunden hat.
     * @return MorphTo
     */
    public function owner() {
        return $this->morphTo();
    }

    /**
     * Synchronisierungen zu diesem System.
     * @return HasMany
     */
    public function synchronizations() {
        return $this->hasMany(Synchronization::class);
    }

    /**
     * Alle Synchronisationen von einem bestimmten Subject zu diesem System.
     * @param Model $subject
     * @return Collection|ProcessVersion[]|Model[]
     */
    public function synchronizationsOf(Model $subject) {
        return $this->synchronizations->where('subject_id', '=', $subject->id)->sortByDesc('created_at');
    }

    /**
     * Synchronisiert eine Prozess-Version dem System.
     * @param Syncable $syncable
     * @param System $system
     * @param License|null $license Falls die Synchronisation mittels einer Lizenz erfolgt.
     * @return Synchronization
     */
    public function sync(Syncable $syncable, System $system, License $license = null) {
        // First we check, if the target system has the required minimum version.
        try {
            $aboutEndpoint = rtrim($this->url, '/') . '/' . config('allisa.api_about_route');
            $response = app(Client::class)->get($aboutEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json'
                ],
            ]);

            $minVersion = config('allisa.min_version');
            $body = json_decode($response->getBody()->getContents());

            if (version_compare($body->version, $minVersion) === -1) {
                $msg = "Die Plattform $this->name ($body->version) benötigt ein Plattform-Update. Bitte aktualisieren Sie die Plattform mindestens auf Version $minVersion.";

                return Synchronization::createFailure($msg, Auth::getUser(), $syncable, $this, $license);
            }
        }
        catch (Throwable $throwable) {
            return Synchronization::createFailure($throwable, Auth::getUser(), $syncable, $this, $license);
        }

        // Then we sync the process version.
        try {
            (app(Client::class))->post($this->url . config('allisa.api_sync_route'), [
                'headers' => ['Authorization' => 'Bearer ' . $this->token],
                'multipart' => [
                    [
                        'name' => 'process_type',
                        'contents' => Storage::get($syncable->definitionExportFilePath()),
                        'filename' => $syncable->definitionExportFileName()
                    ]
                ]
            ]);

            return Synchronization::createSuccess(Auth::getUser(), $syncable, $system, $license);

        }
        catch (Throwable $throwable) {
            return Synchronization::createFailure($throwable, Auth::getUser(), $syncable, $system, $license);
        }
    }

    /**
     * Registriert eine neue Allisa Plattform in der Prozessfabrik.
     * @param string $url
     * @param string $name
     * @param Model $owner
     * @param string $clientId
     * @param string $clientSecret
     * @return void
     * @throws GuzzleException
     */
    public static function register(string $url, string $name, Model $owner, string $clientId, string $clientSecret) {
        // JWT Token erzeugen
        $response = (app(Client::class))->post($url . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'sync-processtypes'
            ]
        ]);

        $jwt = json_decode((string)$response->getBody(), true);
        $accessToken = $jwt['access_token'];
        $expiresAt = Carbon::now()->addSeconds($jwt['expires_in']);

        // System mit JWT Token erstellen
        System::create([
            'name' => $name,
            'url' => $url,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'token' => $accessToken,
            'expires_at' => $expiresAt,
            'owner_id' => $owner->id,
            'owner_type' => $owner::class
        ]);
    }
}
