<?php

namespace App\Models;

use App\Interfaces\Syncable;
use Eloquent;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Repräsentiert eine Synchronization von einem Graph zu einem Allisa System.
 * Class Synchronization
 * @package App\Models
 * @property string $id
 * @property string $system_id
 * @property string $user_id
 * @property string $subject_id
 * @property string $subject_type
 * @property int $response_code
 * @property string $response_message
 * @property Carbon|null $created_at
 * @property-read ProcessVersion $subject
 * @property-read License|null $license
 * @property-read System $system
 * @property-read User $user
 * @method static Builder|Synchronization newModelQuery()
 * @method static Builder|Synchronization newQuery()
 * @method static Builder|Synchronization query()
 * @method static Builder|Synchronization whereCreatedAt($value)
 * @method static Builder|Synchronization whereId($value)
 * @method static Builder|Synchronization whereResponseCode($value)
 * @method static Builder|Synchronization whereResponseMessage($value)
 * @method static Builder|Synchronization whereSubjectId($value)
 * @method static Builder|Synchronization whereSubjectType($value)
 * @method static Builder|Synchronization whereSystemId($value)
 * @method static Builder|Synchronization whereUserId($value)
 * @method static Builder|Synchronization where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|License create(array $attributes = [])
 * @mixin Eloquent
 */
class Synchronization extends Model {

    const UPDATED_AT = null;

    use HasFactory, HasUuids;

    protected $guarded = [];

    /**
     * System zu dem synchronisiert wurde.
     * @return BelongsTo
     */
    public function system() {
        return $this->belongsTo(System::class);
    }

    /**
     * Benutzer, der die Synchronisation durchgeführt hat.
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Lizenz mit diese Synchronisation durchgeführt wurde.
     * @return BelongsTo
     */
    public function license() {
        return $this->belongsTo(License::class);
    }

    /**
     * Model was synchronisiert wurde.
     * @return MorphTo
     */
    public function subject() {
        return $this->morphTo();
    }

    /**
     * Flagge ob die Synchronisation erfolgreich war.
     * @return bool
     */
    public function isSuccess() {
        return $this->response_code === 200;
    }

    /**
     * Flagge ob die Synchronisation zu einem Fehler geführt hat.
     * @return bool
     */
    public function isFailure() {
        return !$this->isSuccess();
    }

    /**
     * Nach erfolgreichen Synchronisationen filtern.
     * @return Synchronization
     * @noinspection PhpUnused
     */
    public static function successes() {
        return self::where('response_code', '=', Response::HTTP_OK);
    }

    /**
     * Erstellt eine erfolgreiche Synchronisation.
     * @param User $user
     * @param Model|Syncable $subject
     * @param System $system
     * @param License|null $license
     * @return Synchronization
     */
    public static function createSuccess(User $user, Model|Syncable $subject, System $system, License $license = null) {
        return self::create([
            'system_id' => $system->id,
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'subject_type' => $subject::class,
            'license_id' => $license?->id,
            'response_code' => Response::HTTP_OK,
            'response_message' => 'Erfolgreich synchronisiert'
        ]);
    }

    /**
     * Erstellt eine erfolgreiche Synchronisation.
     * @param Throwable|string $throwable
     * @param User $user
     * @param Model|Syncable $subject
     * @param System $system
     * @param License|null $license
     * @return Synchronization
     */
    public static function createFailure(Throwable|string $throwable, User $user, Model|Syncable $subject, System $system, License $license = null) {
        if ($throwable instanceof Throwable) {
            $message = $throwable->getMessage();
            $class = $throwable::class;
        }
        else {
            $message = $throwable;
            $class = 'Generic Error';
        }

        Log::warning("Subject $subject::class, ID: $subject->id konnte nicht zum System $system->name ($system->id) synchronisiert werden. Fehler: $class: $message");

        $code = '500';

        if ($throwable instanceof ClientException) {
            $code = $throwable->getCode();
            $message = json_decode((string)$throwable->getResponse()->getBody(), true)['message'] ?? 'Unkown error.';

            // Bei einem Code 422 (Ungültige Eingabedaten) wird davon ausgegangen, dass der Prozess in dem System bereits
            // existiert (Fehler "Prozess existiert bereits"), weshalb hier dennoch ein erfolgreiche Synchronisierung zurückgegeben wird.
            if ($code === Response::HTTP_UNPROCESSABLE_ENTITY) {
                return self::createSuccess($user, $subject, $system, $license);
            }
        }
        if ($throwable instanceof ConnectException) {
            $code = $throwable->getCode();
            $message = 'Es konnte keine Verbindung zu dem System hergestellt werden.';
        }
        if ($throwable instanceof TooManyRedirectsException) {
            $code = $throwable->getCode();
            $message = 'Die Anfrage verursachte zu viele Redirects beim angefragten System.';
        }

        return self::create([
            'system_id' => $system->id,
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'subject_type' => $subject::class,
            'license_id' => $license?->id,
            'response_code' => $code,
            'response_message' => $message
        ]);
    }
}
