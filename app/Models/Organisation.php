<?php

namespace App\Models;

use App\Interfaces\Accessible;
use App\Interfaces\Cachable;
use App\Interfaces\HandlesProcesses;
use App\Interfaces\HandlesRoles;
use App\Traits\HasLicenses;
use App\Traits\HasProcesses;
use App\Traits\UsesAccesses;
use App\Traits\UsesCache;
use App\Traits\UsesRoles;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloCollect;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class Organisation
 * @package App\Models
 * @mixin Eloquent
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $namespace
 * @property string $image
 * @property string $city
 * @property string $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EloCollect|Access[] $accesses
 * @property-read EloCollect|Invitation[] $invitations
 * @property-read EloCollect|Process[] $processes
 * @property-read EloCollect|Role[] $roles
 * @property-read EloCollect|System[] $systems
 * @property-read EloCollect|License[] $licenses
 * @property-read EloCollect|ProcessLicense[] $processLicenses
 * @property-read EloCollect|SolutionLicense[] $solutionLicenses
 * @method static Builder|Organisation newModelQuery()
 * @method static Builder|Organisation newQuery()
 * @method static Builder|Organisation query()
 * @method static Builder|Organisation whereBio($value)
 * @method static Builder|Organisation whereCreatedAt($value)
 * @method static Builder|Organisation whereId($value)
 * @method static Builder|Organisation whereNamespace($value)
 * @method static Builder|Organisation whereUpdatedAt($value)
 * @method static Builder|Organisation whereDescription($value)
 * @method static Builder|Organisation create($value)
 */
class Organisation extends Model implements Accessible, HandlesRoles, Cachable, HandlesProcesses {

    use HasUuids, UsesAccesses, UsesRoles, UsesCache, HasFactory, HasLicenses, HasProcesses;

    protected $guarded = [];

    /**
     * Zugriffe zu der Organisation.
     * @return MorphMany
     */
    public function accesses(): MorphMany {
        return $this->morphMany(Access::class, 'resource');
    }

    /**
     * Einladungen zu der Organisation.
     * @return MorphMany
     */
    public function invitations(): MorphMany {
        return $this->morphMany(Invitation::class, 'resource');
    }

    /**
     * Systeme, die die Entität besitzt.
     * @return MorphMany
     */
    public function systems(): MorphMany {
        return $this->morphMany(System::class, 'owner');
    }

    /**
     * Get organisation plugins.
     * @return MorphMany
     */
    public function plugins(): MorphMany {
        return $this->morphMany(Plugin::class, 'author');
    }

    /**
     * Gibt die Admin-Rolle der Organisation zurück.
     * @return Role
     */
    public function adminRole() {
        return $this->roles->where('is_admin', '=', true)
            ->where('is_owner', '=', false)
            ->first();
    }

    /**
     * Returns the owner role from the organisation.
     * @return Role
     */
    public function ownerRole() {
        return $this->roles->firstWhere('is_owner', '=', true);
    }

    /**
     * Gibt alle Mitglieder der Organisation zurück.
     * @return Collection
     */
    public function members(): Collection {
        $accesses = $this->accesses()->with('recipient')->get();

        return $accesses->pluck('recipient');
    }

    /**
     * Gibt alle Mitglieder einer bestimmten Rolle zurück.
     * @param Role $role
     * @return Collection
     */
    public function membersOfRole(Role $role) {
        $accesses = $this->accesses()->where('role_id', '=', $role->id)->with('recipient')->get();

        return $accesses->pluck('recipient');
    }

    /**
     * Returns the user that has the owner role.
     * @return User|null
     */
    public function ownerMember(): User|null {
        return $this->membersOfRole($this->ownerRole())->first();
    }

    /**
     * Gibt die Rolle eines Benutzers innerhalb der Organisation zurück.
     * @param User $user
     * @return Role|null
     */
    public function roleOf(User $user) {
        $access = $this->accesses->firstWhere('recipient_id', '=', $user->id);

        return $access instanceof Access ? $access->role : null;
    }

    /**
     * Prüft ob ein Benutzer bereits Mitglied der Organisation ist.
     * @param User $user
     * @return bool
     */
    public function isMember(User $user) {
        return Access::whereRecipientId($user->id)->where('resource_id', '=', $this->id)->exists();
    }

    /**
     * Pfad zur internen Profil-Prozesse-Seite der Organisation.
     * @return string
     */
    public function profileProcessesPath(): string {
        return route('organisation.processes', $this);
    }

    /**
     * Pfad zur internen Profil-Solutions-Seite der Organisation.
     * @return string
     */
    public function profileSolutionsPath(): string {
        return route('organisation.solutions', $this);
    }

    /**
     * Pfad zur internen Profil-Lizenzen-Seite der Organisation.
     * @param array $params optionale query parameter.
     * @return string
     */
    public function profileLicensesPath(array $params = []): string {
        return route('organisation.licenses', array_merge(['organisation' => $this], $params));
    }

    /**
     * Pfad zur öffentlichen Profil-Seite der Organisation.
     * @return string
     */
    public function publicPath() {
        return route('organisation.show', $this);
    }

    /**
     * Pfad zu den Einstellungen der Organisation.
     * @return string
     * @noinspection PhpUnused
     */
    public function settingsPath() {
        return route('organisation.settings.data', $this);
    }

    /**
     * Name des Attributes für das Route-Model-Binding
     * sodass der Benutzer via /organisation/{namespace} aufgerufen werden kann
     * @return string
     */
    public function getRouteKeyName() {
        return 'namespace';
    }

    /**
     * @param mixed $value
     * @param null $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null) {
        return $this->whereNamespace($value)->firstOrFail();
    }

    /**
     * Returns the path for the full-size profile image.
     * @return string
     */
    public function imagePath() {
        return Uuid::isValid($this->image) ? public_storage_url($this->image . '.jpg') : $this->image;
    }

    /**
     * Returns the path for the full-size profile image.
     * @return string
     */
    public function thumbnailPath() {
        return Uuid::isValid($this->image) ? public_storage_url($this->image . '_30x.jpg') : $this->image;
    }
}
