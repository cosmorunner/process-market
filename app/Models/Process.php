<?php

namespace App\Models;

use App\Enums\Visibility;
use App\Interfaces\Licensable;
use App\Interfaces\Versionable;
use App\ProcessType\Definition;
use App\Scopes\CustomSoftDeletingScope;
use App\Traits\UsesLicenses;
use App\Traits\UsesVersions;
use Database\Factories\ProcessFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Url\Url;

/**
 * App\Models\Process
 * @property int $id
 * @property string $title
 * @property string $creator_id
 * @property string $description
 * @property string $namespace
 * @property string $identifier
 * @property int $visibility
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $template_id
 * @property array $license_options
 * @property string $latest_version
 * @property string $latest_version_id
 * @property string|null $latest_published_version_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $full_namespace
 * @property-read User $creator
 * @property-read Model|Eloquent|Organisation|User $author
 * @property-read ProcessVersion|null $template
 * @property-read Solution|null $solution
 * @property-read Collection|License $licenses
 * @property-read Collection|Tag[] $tags
 * @property-read Collection|Simulation[] $simulations
 * @property-read Collection|ProcessVersion[] $versions
 * @property-read ProcessVersion $latestVersion
 * @property-read ProcessVersion $latestPublishedVersion
 * @method static Builder|Process newModelQuery()
 * @method static Builder|Process newQuery()
 * @method static Builder|Process query()
 * @method static Builder|Process whereAuthorId($value)
 * @method static Builder|Process whereCreatedAt($value)
 * @method static Builder|Process whereDescription($value)
 * @method static Builder|Process whereId($value)
 * @method static Builder|Process whereTitle($value)
 * @method static Builder|Process whereUpdatedAt($value)
 * @method static Builder|Process whereVisibility($value)
 * @method static Builder|Process whereIdentifier($value)
 * @method static Builder|Process whereLatestVersion($value)
 * @method static Builder|Process whereNamespace($value)
 * @method static Builder|Process whereFullNamespace($value)
 * @method static ProcessFactory factory(...$parameters)
 * @method static Builder|Process whereOwnerId($value)
 * @method static Builder|Process whereOwnerType($value)
 * @method static Builder|Process create($value)
 * @mixin Eloquent
 */
class Process extends Model implements Licensable, Versionable {

    use HasUuids, HasFactory, UsesLicenses, UsesVersions, SoftDeletes;

    /**
     * Hier wird der "boot" vom "SoftDeletes" Scope überschrieben, weil zwar der Trait genutzt
     * werden soll, aber standardgemäß auch die gelöschten Models geladen werden sollen.
     * An einzelnen, erforderlichen Stellen, werden die gelöschten Models aus dem Query entfernt.
     * @return void
     */
    public static function bootSoftDeletes() {
        static::addGlobalScope(new CustomSoftDeletingScope);
    }

    /**
     * Model property type-casting
     * @var array
     */
    protected $casts = [
        'visibility' => 'integer',
        'license_options' => 'array',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Benutzer, der den Prozess ursprünglich erstellt hat.
     * Wird beibehalten, auch wenn eine Organisation oder ein Benutzer den Prozess als Vorlage nutzt.
     * @return BelongsTo
     */
    public function creator() {
        return $this->belongsTo(User::class);
    }

    /**
     * Author des Prozesses. Ein Benutzer oder eine Organisation.
     * @return MorphTo|null
     */
    public function author(): MorphTo|null {
        return $this->morphTo();
    }

    /**
     * Definition des Prozesses. Eine Definition.
     * @return HasMany
     */
    public function definitions(): HasMany {
        return $this->hasMany(Definition::class);
    }

    /**
     * Graph, der als Vorlage für diesen Prozess gedient hat.
     */
    public function template() {
        return $this->belongsTo(ProcessVersion::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags() {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Simulationen des Prozesses.
     * @return HasMany
     */
    public function simulations() {
        return $this->hasMany(Simulation::class);
    }

    /**
     * Pfad zur Metadaten-Bearbeitungsansicht.
     * @return string
     * @noinspection PhpUnused
     */
    public function editPath() {
        return route('process.edit', ['process' => $this]);
    }

    /**
     * Pfad zur Entwicklung des Prozesses.
     * @param string|null $version
     * @return string
     */
    public function devPath(string $version = null) {
        return route('process.develop', ['process' => $this, 'version' => $version]);
    }

    /**
     * Pfad zur Konfiguration des Prozesses.
     * @param string|null $version
     * @return string
     */
    public function configPath(string $version = null) {
        return route('process.config', ['process' => $this, 'version' => $version]);
    }

    /**
     * Gibt den Pfad zum Profil (Prozesse-Tab) des Eigentümers (Person oder Organisation zurück).
     * @return string
     */
    public function authorProfileProcessesPath() {
        if ($this->author instanceof Organisation || $this->author instanceof User) {
            return $this->author->profileProcessesPath();
        }

        return route('index');
    }

    /**
     * Gibt den Pfad zum Profil des Eigentümers (Person oder Organisation zurück).
     * @return string
     * @noinspection PhpUnused
     */
    public function authorPublicPath(): string {
        if ($this->author instanceof Organisation || $this->author instanceof User) {
            return $this->author->publicPath();
        }

        return route('index');
    }

    /**
     * Path to a process demo.
     * @param string $version
     * @return string
     */
    public function demoPath(string $version) {
        return route('process.demo', ['process' => $this, 'version' => $version]);
    }

    /**
     * Pfad zur öffentlichen Detail-Ansicht des Prozesses.
     */
    public function publicPath(): string {
        return route('process.detail', ['namespace' => $this->namespace, 'identifier' => $this->identifier]);
    }

    /**
     * Laufende Simulationen
     * @return Collection
     */
    public function runningSimulations() {
        return $this->simulations()->where('finished_at', '=', null)->get();
    }

    /**
     * Gibt die Tags des Prozesses als Semikolon-separierten String zurück.
     * @return string
     */
    public function tagsToString() {
        return $this->tags->pluck('name')->implode(';');
    }

    /**
     * Setzt die Sichtbarkeit des Prozesses.
     * @param int $visibility Sichtbarkeit des Prozesses.
     */
    public function updateVisibility($visibility) {
        $this->update(['visibility' => $visibility]);
    }

    /**
     * Flag if the process belongs to an organisation.
     * @param Organisation|null $organisation Optional check if the process belongs to a specific organisation.
     * @return bool
     */
    public function authoredByOrganisation(Organisation $organisation = null): bool {
        if ($organisation) {
            return $organisation->id === $this->author_id;
        }

        return $this->author_type === Organisation::class;
    }

    /**
     * Flag if the process belongs to a user.
     * @param User|null $user Optional check if the process belongs to a specific user.
     * @return bool
     */
    public function authoredByUser(User $user = null): bool {
        if ($user) {
            return $user->id === $this->author_id;
        }

        return $this->author_type === User::class;
    }

    /**
     * Checks, whether the process is publicly available.
     */
    public function isPubliclyAccessible() {
        return in_array($this->visibility, [Visibility::Public->value, Visibility::Hidden->value]);
    }

    /**
     * Flagge, ob der Prozess archiviert wurde.
     * @return bool
     * @noinspection PhpUnused
     */
    public function isArchived(): bool {
        return $this->deleted_at !== null;
    }

    /**
     * Query-Filter, bei dem ausschließlich öffentliche Prozesse zurückgegeben werden.
     * @return Builder
     */
    public static function public() {
        return self::whereVisibility(Visibility::Public->value);
    }

    /**
     * Creates the initial version based on the "empty" template in "graph.php".
     * @return ProcessVersion
     */
    public function createInitialVersion(): ProcessVersion {
        return ProcessVersion::create([
            'process_id' => $this->id,
            'calculated' => config('graph.empty_calculated'),
            'definition' => array_merge(config('graph.empty'), [
                'name' => $this->title,
                'namespace' => $this->namespace,
                'identifier' => $this->identifier
            ]),
            'version' => 'develop',
            'full_namespace' => $this->namespace . '/' . $this->identifier . '@develop',
            'complexity_score' => 1.0,
            'published_at' => null
        ]);
    }

    /**
     * @param array $validatedData
     * @return string Redirect Url
     */
    public static function storeLicense(array $validatedData): string {
        $process = Process::whereId($validatedData['process_id'])->first();
        $receiver = User::whereNamespace($validatedData['receiver'])
            ->first() ?? Organisation::whereNamespace($validatedData['receiver'])->first();

        // Lizenz erstellen
        $process->createLicense($validatedData['license'], $receiver);

        $redirectUrl = Url::fromString($receiver->profileLicensesPath());
        $redirectUrl = $redirectUrl->withQueryParameter('fm', base64_encode(sprintf('Herzlichen Glückwunsch, Sie haben eine Lizenz für den Prozess "%s - %s" erworben!', $process->title, $process->full_namespace)));

        return $redirectUrl->__toString();
    }

    /**
     * Flagge ob der Benutzer Zugriff auf die privaten Demo-Umgebungen der Prozess-Version hat.
     * @param User $user
     * @return bool
     */
    public function enabledPrivateEnvironments(User $user): bool {
        if ($this->authoredByUser()) {
            return $user->namespace === $this->namespace;
        }
        else {
            return $user->hasAccessTo($this->author) || $user->hasLicense($this);
        }
    }
}