<?php

namespace App\Models;

use App\Traits\UsesAccesses;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use App\ProcessType\Role as ProcessTypeRole;

/**
 * Beschreibt den Zugriff auf Ressourcen (Gruppen, Prozess-Instanzen, Prozesstypen) von Gruppen oder Benutzers
 * mittels Rollen.
 * Class Access
 * @package App
 * @property string $id
 * @property string $role_id
 * @property string|null $resource_id
 * @property string|null $resource_type
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|UsesAccesses $recipient
 * @property-read Process $resource
 * @property-read Role $role
 * @method static Builder|Access newModelQuery()
 * @method static Builder|Access newQuery()
 * @method static Builder|Access query()
 * @method static Builder|Access whereCreatedAt($value)
 * @method static Builder|Access whereId($value)
 * @method static Builder|Access whereRecipientId($value)
 * @method static Builder|Access whereRecipientType($value)
 * @method static Builder|Access whereResourceId($value)
 * @method static Builder|Access whereResourceType($value)
 * @method static Builder|Access whereRoleId($value)
 * @method static Builder|Access whereUpdatedAt($value)
 * @method static Builder|Access create($value)
 * @mixin Eloquent
 */
class Access extends Model {

    use HasUuids, HasFactory;

    /**
     * "updated_at" - Timestamp existiert nicht.
     */
    const UPDATED_AT = null;

    protected $guarded = [];

    /**
     * Die Ressource zu der Zugriff gewährt wird.
     * @return MorphTo
     */
    public function resource() {
        return $this->morphTo('resource');
    }

    /**
     * Der Empfänger des Zugriffs zu der Ressource.
     * @return MorphTo
     */
    public function recipient() {
        return $this->morphTo('recipient');
    }

    /**
     * Der Empfänger des Zugriffs zu der Ressource.
     * @return BelongsTo
     */
    public function role() {
        return $this->belongsTo(Role::class);
    }

    /**
     * Fügt einen System-Zugriff, Gruppen-Zugriff oder Prozess-Instanz-Zugriff
     * für einen den Benutzer mit einer Rolle hinzu.
     * @param null|UsesAccesses|Process $resource
     * @param User $recipient
     * @param Role|ProcessTypeRole $role
     * @return Access
     */
    public static function grant($resource, $recipient, $role) {
        return Access::create([
            'recipient_id' => $recipient->id,
            'recipient_type' => $recipient::class,
            'resource_id' => $resource === null ? $resource : $resource->id,
            'resource_type' => $resource === null ? $resource : $resource::class,
            'role_id' => $role->id
        ]);
    }

    /**
     * Prüft ob der Access ein Zugriff auf ein bestimmtes Recht gewährt.
     * @param string $ident
     * @return bool
     */
    public function allows(string $ident) {
        if (!$this->role instanceof Role) {
            return false;
        }

        return $this->role->can($ident);
    }
}

