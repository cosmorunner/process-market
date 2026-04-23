<?php

namespace App\Models;

use App\Interfaces\Licensable;
use Database\Factories\LicenseFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Lizenz für einen Prozess, Lösung oder
 * @property string $id
 * @property string $resource_type
 * @property string $resource_id
 * @property string $owner_type
 * @property string $owner_id
 * @property string $issuer_id
 * @property string $level
 * @property array $level_options
 * @property Carbon|null $created_at
 * @property-read Licensable|Process|Solution $resource
 * @property-read User|Organisation $owner
 * @property-read Collection|Synchronization[] $synchronizations
 * @property-read User $issuer
 * @method static LicenseFactory factory(...$parameters)
 * @method static Builder|License newModelQuery()
 * @method static Builder|License newQuery()
 * @method static Builder|License query()
 * @method static Builder|License whereCreatedAt($value)
 * @method static Builder|License whereId($value)
 * @method static Builder|License whereIssuerId($value)
 * @method static Builder|License whereLevel($value)
 * @method static Builder|License whereLevelOptions($value)
 * @method static Builder|License whereOwnerId($value)
 * @method static Builder|License whereOwnerType($value)
 * @method static Builder|License whereResourceId($value)
 * @method static Builder|License whereResourceType($value)
 * @method static Builder|License where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|License create(array $attributes = [])
 * @mixin Eloquent
 */
class License extends Model {

    const UPDATED_AT = null;
    const LEVEL_PRIVATE = 'private';
    const LEVEL_OPEN_SOURCE = 'open-source';
    const LEVEL_NO_LICENSE = 'no-license';
    const LEVEL_MIXED = 'mixed';
    const LEVELS = [
        self::LEVEL_PRIVATE,
        self::LEVEL_OPEN_SOURCE,
        self::LEVEL_MIXED,
        self::LEVEL_NO_LICENSE
    ];

    use HasFactory, HasUuids;

    protected $casts = ['level_options' => 'array'];

    protected $guarded = [];

    /**
     * Die Ressource zu der die Lizenz gehört, z.B. ein Prozess, Plugin oder Lösung.
     * @return MorphTo
     */
    public function resource() {
        return $this->morphTo('resource');
    }

    /**
     * Eigentümer der Lizenz.
     * @return MorphTo
     */
    public function owner() {
        return $this->morphTo('owner');
    }

    /**
     * Synchronisationen dieser Lizenz.
     * @return HasMany
     */
    public function synchronizations() {
        return $this->hasMany(Synchronization::class);
    }

    /**
     * Ersteller der Lizenz.
     * @return BelongsTo
     */
    public function issuer() {
        return $this->belongsTo(User::class);
    }

    /**
     * Gibt den Pfad zum Profil (Prozesse-Tab) des Eigentümers (Person oder Organisation zurück).
     * @param array $params optionale query parameter.
     * @noinspection PhpUnused
     * @return string
     */
    public function ownerProfileLicensesPath(array $params = []) {
        if ($this->owner instanceof Organisation || $this->owner instanceof User) {
            return $this->owner->profileLicensesPath($params);
        }

        return route('index');
    }

    /**
     * Flagge, ob die Lizenz Eigentum eines Benutzers ist.
     * @param User|null $user Optionale Angabe, ob die Lizenz einem bestimmten Benutzer gehört.
     * @return bool
     */
    public function ownedByUser(User $user = null) {
        return $user ? $this->owner_id === $user->id : $this->owner_type === User::class;
    }

    /**
     * Flagge, ob die Lizenz Eigentum eines Benutzers ist.
     */
    public function ownedByOrganisation() {
        return $this->owner_type === Organisation::class;
    }

    /**
     * Flagge, ob die Lizenz eine Open-Source Lizenz ist.
     */
    public function isOpenSource() {
        return $this->level === self::LEVEL_OPEN_SOURCE;
    }

    /**
     * Titel der Lizenz.
     * @return string
     */
    public function typeTitle(): string {
        return match ($this->level) {
            self::LEVEL_OPEN_SOURCE => 'Open-Source',
            self::LEVEL_PRIVATE => 'Privat',
            self::LEVEL_MIXED => 'Mixed',
            self::LEVEL_NO_LICENSE => 'Auf Anfrage',
            default => '',
        };
    }

    /**
     * Returns the current query in the scope of the provided owner.
     * @param Builder $query
     * @param User|Organisation $owner
     * @return Builder
     * @noinspection PhpUnused
     */
    public static function scopeOfOwner(Builder $query, User|Organisation $owner) {
        return $query->where([
            'owner_type' => get_class($owner),
            'owner_id' => $owner->id
        ]);
    }

    /**
     * Checks whether a license already exists for the given parameters.
     * @param Licensable $licensable
     * @param User|Organisation $receiver
     * @param string|null $level
     * @return License|null
     */
    public static function identify(Licensable $licensable, User|Organisation $receiver, string $level = null): License|null {
        $where = [
            'resource_id' => $licensable->id,
            'resource_type' => $licensable::class,
            'owner_id' => $receiver->id,
            'owner_type' => $receiver::class,
        ];

        if ($level) {
            $where['level'] = $level;
        }

        return License::where($where)->first();
    }
}
