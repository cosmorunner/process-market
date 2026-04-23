<?php /** @noinspection PhpUnused */

namespace App\Models;

use App\Enums\Visibility;
use App\Interfaces\Licensable;
use App\Interfaces\Versionable;
use App\Scopes\CustomSoftDeletingScope;
use App\Traits\UsesLicenses;
use App\Traits\UsesVersions;
use Database\Factories\SolutionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Url\Url;

/**
 * App\Models\Solution
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $creator_id
 * @property string $namespace
 * @property string $identifier
 * @property string $full_namespace
 * @property int $visibility
 * @property string $latest_version
 * @property string|null $author_id
 * @property string|null $author_type
 * @property User|Organisation|null $author
 * @property string|null $latest_version_id
 * @property string|null $latest_published_version_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Collection|SolutionVersion[] $versions
 * @property Collection|Demo[] $demos
 * @property array $license_options
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Collection|License $licenses
 * @property-read SolutionVersion $latestVersion
 * @property-read SolutionVersion $latestPublishedVersion
 * @method static SolutionFactory factory(...$parameters)
 * @method static Builder|Solution newModelQuery()
 * @method static Builder|Solution newQuery()
 * @method static Builder|Solution query()
 * @method static Builder|Solution find($value)
 * @method static Builder|Solution whereAuthorId($value)
 * @method static Builder|Solution whereAuthorType($value)
 * @method static Builder|Solution whereCreatedAt($value)
 * @method static Builder|Solution whereCreatorId($value)
 * @method static Builder|Solution whereDescription($value)
 * @method static Builder|Solution whereId($value)
 * @method static Builder|Solution whereIdentifier($value)
 * @method static Builder|Solution whereLatestPublishedSolutionsVersionId($value)
 * @method static Builder|Solution whereLatestSolutionsVersionId($value)
 * @method static Builder|Solution whereLatestVersion($value)
 * @method static Builder|Solution whereName($value)
 * @method static Builder|Solution whereNamespace($value)
 * @method static Builder|Solution whereFullNamespace($value)
 * @method static Builder|Solution whereUpdatedAt($value)
 * @method static Builder|Solution whereVisibility($value)
 * @method static Builder|Solution create($value)
 * @mixin Eloquent
 */
class Solution extends Model implements Licensable, Versionable {

    use HasFactory, HasUuids, UsesLicenses, UsesVersions, SoftDeletes;

    /**
     * Model property type-casting
     * @var array
     */
    protected $casts = [
        'visibility' => 'integer',
        'license_options' => 'array'
    ];

    protected $guarded = [];

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
     * Benutzer, der die Lösung ursprünglich erstellt hat.
     * Wird beibehalten, auch wenn eine Organisation oder ein Benutzer den Prozess als Vorlage nutzt.
     * @return BelongsTo
     */
    public function creator() {
        return $this->belongsTo(User::class);
    }

    /**
     * Author der Lösung. Ein Benutzer oder eine Organisation.
     * @return MorphTo|null
     */
    public function author(): MorphTo|null {
        return $this->morphTo();
    }

    /**
     * Alle Prozesse aller Versionen.
     * @return Collection
     */
    public function processes() {
        $processNamespaces = $this->versions->pluck('data.process_types')
            ->flatten(1)
            ->map(fn(string $item) => namespace_parts($item)['namespace'] . '/' . namespace_parts($item)['identifier'])
            ->unique();

        return Process::whereIn('full_namespace', $processNamespaces)->get();
    }

    /**
     * Lädt von allen Versionen die Graphen.
     * @param array $loadRelations Relations laden
     * @return void
     */
    public function loadProcessVersionsOfVersions(array $loadRelations = []) {
        $versions = $this->versions()->with($loadRelations)->get();
        $this->setRelation('versions', $versions);

        $namespaces = $versions->pluck('data.process_types')->flatten(1)->unique()->filter(fn($item) => is_string($item));
        $processNamespaces = $namespaces->map(fn(string $fullNamespace) => explode('@', $fullNamespace)[0])->unique();
        $processes = Process::with(['versions', 'versions.process', 'versions.process.author'])
            ->whereIn('full_namespace', $processNamespaces)
            ->get();

        /* @var SolutionVersion $solutionVersion */
        foreach ($this->versions as $solutionVersion) {
            $processVersions = collect();

            foreach ($solutionVersion->processTypes() as $processType) {
                /* @var Process $process */
                $process = $processes->firstWhere('full_namespace', explode('@', $processType)[0]);

                if ($process && ($processVersion = $process->version(explode('@', $processType)[1]))) {
                    $processVersions = $processVersions->add($processVersion);
                }
            }

            if ($processVersions->isNotEmpty()) {
                $solutionVersion->cachedProcessVersions = $processVersions;
            }
        }
    }

    /**
     * Pfad zur Öffentlichen Ansicht der Lösung.
     * @return string
     */
    public function publicPath() {
        return route('solution.detail', ['namespace' => $this->identifier, 'identifier' => $this->identifier]);
    }

    /**
     * Demos der Lösung.
     * @return HasMany
     */
    public function demos() {
        return $this->hasMany(Demo::class);
    }

    /**
     * Laufende Demos.
     * @return Collection
     */
    public function runningDemos() {
        return $this->demos()->where('finished_at', '=', null)->get();
    }

    /**
     * Pfad zur Metadaten-Bearbeitungsansicht.
     * @return string
     * @noinspection PhpUnused
     */
    public function editPath() {
        return route('solution.edit', ['solution' => $this]);
    }

    /**
     * Pfad zur Konfigurationsansicht.
     * @return string
     * @noinspection PhpUnused
     */
    public function configPath() {
        return route('solution.config', ['solution' => $this]);
    }

    /**
     * Pfad zur öffentlichen Detail-Ansicht der Lösung.
     * @noinspection PhpUnused
     */
    public function detailPath(): string {
        return route('solution.detail', [
            'namespace' => $this->namespace,
            'identifier' => $this->identifier
        ]);
    }

    /**
     * Die aktuellste Version, bei der alle konfigurierten Prozesse mindestens die
     * Sichtbarkeit "Versteckt" haben.
     * @param string|null $version Bei Angabe einer Version wird die aktuellste Version zurückgegeben die sowohl
     * publiziert wurde und keine privaten Prozesse hat.
     * @return Collection|SolutionVersion|null
     */
    public function publicVersions(string $version = null): Collection|SolutionVersion|null {
        $publicVersions = $this->publishedVersions()
            ->filter(fn(SolutionVersion $solutionVersion) => !$solutionVersion->hasPrivateProcess());

        if (!$version) {
            return $publicVersions;
        }

        if ($version == 'latest') {
            return $publicVersions->first();
        }

        return $publicVersions->firstWhere('version', '=', $version);
    }

    /**
     * Flagge, ob der Prozess zu einer Organisation gehört.
     * @return bool
     */
    public function authoredByOrganisation(): bool {
        return $this->author_type === Organisation::class;
    }

    /**
     * Flagge, ob der Prozess zu einem Benutzer gehört.
     * @return bool
     */
    public function authoredByUser(): bool {
        return $this->author_type === User::class;
    }

    /**
     * Setzt die Sichtbarkeit der Lösung.
     * @param int $visibility Sichtbarkeit des Prozesses.
     */
    public function updateVisibility($visibility) {
        $this->update(['visibility' => $visibility]);
    }

    /**
     * @return BelongsToMany
     */
    public function tags() {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Gibt die Tags der Solution als Semikolon-separierten String zurück.
     * @return string
     */
    public function tagsToString() {
        return $this->tags->pluck('name')->implode(';');
    }

    /**
     * Gibt den Pfad zum Profil (Prozesse-Tab) des Eigentümers (Person oder Organisation zurück).
     * @return string
     */
    public function authorProfileSolutionPath() {
        if ($this->author instanceof Organisation || $this->author instanceof User) {
            return $this->author->profileSolutionsPath();
        }

        return route('index');
    }

    /**
     * Prüft, ob die Solution öffentlich erreichbar ist.
     */
    public function isPubliclyAccessible() {
        return $this->visibility === Visibility::Public->value || $this->visibility === Visibility::Hidden->value;
    }

    /**
     * Query-Filter, bei dem ausschließlich öffentliche Prozesse zurückgegeben werden.
     * @return Builder
     */
    public static function public() {
        return self::whereVisibility(Visibility::Public->value);
    }

    /**
     * Gibt den Namespace mit Identifier ohne Version zurück.
     * @return Attribute
     */
    protected function fullNamespace(): Attribute {
        return Attribute::make(get: fn(mixed $value, array $attributes) => $attributes['namespace'] . '/' . $attributes['identifier']);
    }

    /**
     * Gibt die Demo der aktuellsten, nicht veröffentlichten Lösungsversion zurück.
     * @return Demo|null
     */
    public function mainDemo() {
        return $this->latestVersion->mainDemo();
    }

    /**
     * @param array $validatedData
     * @return string $redirect Redirect-Url
     */
    public static function storeLicense(array $validatedData): string {
        $solution = Solution::find($validatedData['resource_id']);
        $receiver = User::whereNamespace($validatedData['receiver'])
            ->first() ?? Organisation::whereNamespace($validatedData['receiver'])->first();

        // Lizenz erstellen
        $solution->createLicense($validatedData['license'], $receiver);

        $redirectUrl = Url::fromString($receiver->profileLicensesPath());
        $redirectUrl = $redirectUrl->withQueryParameter('fm', base64_encode(sprintf('Herzlichen Glückwunsch, Sie haben eine Lizenz für "%s - %s" erworben!', $solution->name, $solution->full_namespace)));

        return $redirectUrl->__toString();
    }
}
