<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Einladung zum System oder zu einer Organisation.
 * Class Invitation
 * @package App\Models
 * @property string $id
 * @property string $email
 * @property string|null $resource_id
 * @property string|null $resource_type
 * @property string $role_id
 * @property string $creator_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null|Carbon $expires_at
 * @property-read Model|Eloquent|Organisation $resource
 * @property-read Role|null $role
 * @property-read User|null $creator
 * @method static Builder|Invitation newModelQuery()
 * @method static Builder|Invitation newQuery()
 * @method static Builder|Invitation query()
 * @method static Builder|Invitation whereCreatedAt($value)
 * @method static Builder|Invitation whereEmail($value)
 * @method static Builder|Invitation whereExpiresAt($value)
 * @method static Builder|Invitation whereId($value)
 * @method static Builder|Invitation whereResourceId($value)
 * @method static Builder|Invitation whereResourceType($value)
 * @method static Builder|Invitation whereRoleId($value)
 * @method static Builder|Invitation whereIdentityMetaType($value)
 * @method static Builder|Invitation whereIdentityProcessType($value)
 * @method static Builder|Invitation whereCreatorId($value)
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder create(array $attributes = [])
 * @mixin Eloquent
 */
class Invitation extends Model {

    use HasFactory, HasUuids;

    /**
     * "updated_at" - Timestamp existiert nicht.
     */
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Die Ressource zu der eingeladen wird.
     * @return MorphTo
     */
    public function resource() {
        return $this->morphTo('resource');
    }

    /**
     * Organisations-Rolle
     * @return BelongsTo
     */
    public function role() {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return HasOne
     * @noinspection PhpUnused
     */
    public function creator() {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

    /**
     * Gibt alle Systemeinladungen zurück.
     * @return Invitation[]|Builder[]|Collection
     */
    public static function systemInvitations() {
        return self::whereResourceType(null)->get();
    }

    /**
     * Löscht alle abgelaufenen Einladungen.
     * Wird bei InvitationObserver::created() aufgerufen.
     */
    public static function deleteExpiredInvitations() {
        try {
            Invitation::where('expires_at', '<', Carbon::now())->delete();
        }
        catch (Exception) {
            // Ignore
        }
    }

    /**
     * Gibt zurück, ob die Einladung noch gültig ist indem das Ablaufdatum, die Resource, die Rolle und
     * die Abwesenheit eines Benutzers mit der E-Mail-Adresse geprüft wird.
     * @return bool
     */
    public function isValid() {
        $isNotExpired = $this->expires_at > Carbon::now();
        $userDoesNotExistYet = User::withTrashed()->where('email', '=', $this->email)->doesntExist();

        // System-Einladung
        if ($this->resource === null) {
            return $isNotExpired && $userDoesNotExistYet && $this->role === null;
        }

        // Organisationseinladung
        return $isNotExpired && $this->role instanceof Role && $this->resource instanceof Organisation;
    }

    /**
     * Gibt zurück, ob die Einladung noch gültig ist indem das Ablaufdatum, die Resource, die Rolle und
     * die Abwesenheit eines Benutzers mit der E-Mail-Adresse geprüft wird.
     * @return bool
     */
    public function isInvalid() {
        return !$this->isValid();
    }

    /**
     * Erneuert das Ablaufdataum.
     */
    public function renewExpiry() {
        $this->update([
            'expires_at' => Carbon::now()->addWeek()
        ]);
    }
}
