<?php

namespace App\Models;

use App\Environment\Group;
use App\Interfaces\Accessible;
use App\Interfaces\Cachable;
use App\Interfaces\HandlesProcesses;
use App\Notifications\PasswordReset;
use App\Notifications\VerifyEmail;
use App\Traits\HasLicenses;
use App\Traits\HasProcesses;
use App\Traits\UsesCache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\User
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $namespace
 * @property string $image
 * @property string $city
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property string|null $demo_identifier
 * @property string|null $context
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Organisation|null $contextModel
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Process[] $processes
 * @property-read int|null $processes_count
 * @property-read Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereApiToken($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereNamespace($value)
 * @method static Builder|User whereContext($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User create($value)
 * @mixin Eloquent
 * @property string|null $bio
 * @method static Builder|User whereBio($value)
 * @property-read Collection|Organisation[] $organisations
 * @property-read Collection|Access[] $accesses
 * @property-read Collection|Access[] $organisationAccesses
 * @property-read Collection|System[] $systems
 * @property-read Collection|License[] $licenses
 * @property-read Collection|ProcessLicense[] $processLicenses
 * @property-read Collection|SolutionLicense[] $solutionLicenses
 * @property-read int|null $organisations_count
 * @property-read int|null $systems_count
 * @property Carbon|null $deleted_at
 * @method static QueryBuilder|User onlyTrashed()
 * @method static Builder|User whereDeletedAt($value)
 * @method static QueryBuilder|User withTrashed()
 * @method static QueryBuilder|User withoutTrashed()
 */
class User extends Authenticatable implements Cachable, HandlesProcesses {

    use HasApiTokens, Notifiable, HasUuids, HasLicenses, HasFactory, SoftDeletes, UsesCache, HasProcesses;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'email',
        'bio',
        'demo_identifier',
        'image',
        'city',
        'context'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Gruppen in denen der Benutzer Mitglied ist.
     * @return HasManyThrough
     */
    public function organisations() {
        return $this->hasManyThrough(Organisation::class, Access::class, 'recipient_id', 'id', null, 'resource_id');
    }

    /**
     * Returns the users accesses to organisations including the organisation (resource) and role.
     * @return HasMany
     */
    public function organisationAccesses() {
        return $this->accesses()->where('resource_type', '=', Organisation::class)->with(['role', 'resource']);
    }

    /**
     * All accesses of the user.
     * @return HasMany
     */
    public function accesses() {
        return $this->hasMany(Access::class, 'recipient_id');
    }

    /**
     * Systeme, die die Entität besitzt.
     * @return MorphMany
     */
    public function systems(): MorphMany {
        return $this->morphMany(System::class, 'owner');
    }

    /**
     * Get user plugins.
     * @return MorphMany
     */
    public function plugins() {
        return $this->morphMany(Plugin::class, 'author');
    }

    /**
     * Returns null or the organisation model.
     * @return BelongsTo
     */
    public function contextModel(): BelongsTo {
        return $this->belongsTo(Organisation::class, 'context');
    }

    /**
     * Relative URL zum Benutzer.
     * @return string
     */
    public function publicPath() {
        return route('user.show', ['user' => $this]);
    }

    /**
     * Gibt den Pfad zum Profil (Prozesse-Tab) des Benutzers zurück.
     */
    public function profileProcessesPath(): string {
        return route('profile.processes');
    }

    /**
     * Gibt den Pfad zum Profil (Solutions-Tab) des Benutzers zurück.
     */
    public function profileSolutionsPath(): string {
        return route('profile.solutions');
    }

    /**
     * Gibt den Pfad zum Profil (Lizenzen-Tab) des Benutzers zurück.
     * @param array $params optionale query parameter.
     */
    public function profileLicensesPath(array $params = []): string {
        return route('profile.licenses', $params);
    }

    /**
     * Name des Attributes für das Route-Model-Binding, sodass der Benutzer via /user/{namespace} aufgerufen werden kann.
     * @return string
     */
    public function getRouteKeyName() {
        return 'namespace';
    }

    /**
     * Name des Attributes für das Route-Model-Binding, sodass der Benutzer via /user/{namespace} aufgerufen werden kann.
     * @return Attribute
     */
    protected function name(): Attribute {
        return Attribute::make(get: fn(mixed $value, array $attributes) => $attributes['namespace']);
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
     * Checks if the user has access to a specific resource.
     * @param Accessible|Organisation $accessible
     * @return bool
     */
    public function hasAccessTo($accessible) {
        return $accessible instanceof Accessible && $this->accessTo($accessible) instanceof Access;
    }

    /**
     * Return the access of an accessible resource.
     * @param Accessible|Group $resource
     * @return Access|null
     */
    public function accessTo($resource) {
        if (!$resource instanceof Accessible) {
            return null;
        }

        return Access::whereResourceId($resource->id)
            ->where(fn($query) => $query->where('recipient_id', '=', $this->id))
            ->first();
    }

    /**
     * Gibt die Rolle des Benutzers innerhalb der zugreifbaren Resource zurück.
     * @param Accessible|Organisation|Process $accessible
     * @return Role|null
     */
    public function roleWithin($accessible) {
        if (!$accessible instanceof Accessible) {
            return null;
        }

        $access = $this->accessTo($accessible);

        return $access instanceof Access ? $access->role : null;
    }

    /**
     * Prüfen ob der Benutzer in bestimmtes Recht innerhalb einer Organisation hat.
     * @param Organisation $organisation
     * @param string $permissionIdent
     * @return bool
     */
    public function hasOrganisationPermission(Organisation $organisation, string $permissionIdent): bool {
        return $this->roleWithin($organisation)?->can($permissionIdent);
    }

    /**
     * Prüft ob der Benutzer einer Entität gehört.
     * @param Model $model
     * @return bool
     */
    public function authored(Model $model) {
        return $model->author_id === $this->id;
    }

    /**
     * Passwort-Zurücksetzen E-Mail.
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new PasswordReset($token));
    }

    /**
     * Sendet die E-Mail-Bestätigung.
     * @return void
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail());
    }

    /**
     * Flagge, ob der Benutzer bereits eine Demo hat.
     * @return bool
     */
    public function hasDemo(): bool {
        return (bool) $this->demo_identifier;
    }

    /**
     * Returns the path for the full-size profile image.
     * @return string
     */
    public function imagePath() {
        return Uuid::isValid($this->image) ? public_storage_url($this->image . '.jpg') : $this->image;
    }

    /**
     * Returns the path for the thumbnail sized profile image.
     * @return string
     */
    public function thumbnailPath() {
        return Uuid::isValid($this->image) ? public_storage_url($this->image . '_30x.jpg') : $this->image;
    }

}
