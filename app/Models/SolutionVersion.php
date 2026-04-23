<?php /** @noinspection PhpUnused */

namespace App\Models;

use App\Enums\Visibility;
use Carbon\Carbon;
use Database\Factories\SolutionVersionFactory;
use Eloquent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * App\Models\SolutionVersion
 * @property int $id
 * @property string $solution_id
 * @property mixed $data
 * @property string $version
 * @property string $full_namespace
 * @property string|null $changelog
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Solution|null $solution
 * @property float $complexity_score
 * @property-read \Illuminate\Database\Eloquent\Collection|Synchronization[] $synchronizations
 * @method static SolutionVersionFactory factory(...$parameters)
 * @method static Builder|SolutionVersion newModelQuery()
 * @method static Builder|SolutionVersion newQuery()
 * @method static Builder|SolutionVersion query()
 * @method static Builder|SolutionVersion whereChangelog($value)
 * @method static Builder|SolutionVersion whereCreatedAt($value)
 * @method static Builder|SolutionVersion whereData($value)
 * @method static Builder|SolutionVersion whereId($value)
 * @method static Builder|SolutionVersion wherePublishedAt($value)
 * @method static Builder|SolutionVersion whereSolutionId($value)
 * @method static Builder|SolutionVersion whereUpdatedAt($value)
 * @method static Builder|SolutionVersion whereVersion($value)
 * @method static Builder|SolutionVersion create($value)
 * @mixin Eloquent
 */
class SolutionVersion extends Model {

    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'published_at' => 'date'
    ];

    public ?Collection $cachedProcessVersions = null;

    /**
     * @return BelongsTo
     */
    public function solution() {
        return $this->belongsTo(Solution::class);
    }

    /**
     * Flagge ob die Version bereits fertiggestellt wurde.
     * @return bool
     */
    public function isPublished() {
        return $this->published_at !== null;
    }

    /**
     * Simulationen des Graphen.
     * @return HasMany
     */
    public function demos() {
        return $this->hasMany(Demo::class);
    }

    /**
     * Synchronisationen von diesem Graph.
     * @return MorphMany
     */
    public function synchronizations(): MorphMany {
        return $this->morphMany(Synchronization::class, 'subject');
    }

    /**
     * Gibt alle Synchronisationen von einer Vielzahl von Systemen zurück.
     * @param Collection $systems
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function synchronizationsOfSystems(Collection $systems) {
        return $this->synchronizations->whereIn('system_id', $systems->pluck('id'));
    }

    /**
     * Gibt die unter "graphs" im "data"-Attribut vermerkten Prozess-Versionen zurück.
     * @return Collection
     */
    public function processVersions() {
        if ($this->cachedProcessVersions instanceof Collection) {
            return $this->cachedProcessVersions;
        }
        $namespaces = collect($this->processTypes() ?? [])->map(fn($item) => explode('@', $item)[0])->unique();
        $processes = Process::with(['versions', 'versions.process'])->whereIn('full_namespace', $namespaces)->get();

        $this->cachedProcessVersions = collect($this->processTypes() ?? [])->map(function ($fullNamespaceWithVersion) use ($processes) {
            /* @var Process $process */
            $parts = namespace_parts($fullNamespaceWithVersion);
            $process = $processes->firstWhere('full_namespace', '=', $parts['namespace'] . '/' . $parts['identifier']);

            return $process->version($parts['version'], true);
        });

        return $this->cachedProcessVersions;
    }

    /**
     * Die Prozesse der genutzten Graphen
     * @return Collection
     */
    public function processes() {
        return $this->processVersions()->pluck('process')->unique('id');
    }

    /**
     * Flagge, ob diese Version einen Prozess konfiguriert hat, der privat ist.
     * @return bool
     */
    public function hasPrivateProcess(): bool {
        return $this->processes()
            ->reduce(fn($carry, Process $process) => $carry || $process->visibility === Visibility::Private->value, false);
    }

    /**
     * Wenn ein Benutzer eingeloggt ist, wird eine eventuell laufende Simulation des Graphen zurückgegeben,
     * andernfalls null.
     * @return Demo|null
     */
    public function runningUserDemo(): Demo|null {
        /* @var User $user */
        $user = auth()->user();

        return $user?->runningUserDemos($this);
    }

    /**
     * Publiziert den Graph, indem der Graph dupliziert wird, als "published" markiert wird
     * die Graph-Id im Prozess eingetragen wird.
     * @param string $version
     * @param string|null $changelog
     * @return SolutionVersion
     */
    public function publish(string $version, ?string $changelog = null): SolutionVersion {
        $oldData = $this->data;

        //Fix process type versions, i.e. determine the actual, current version number from "allisa/demo@latest".
        $graphs = $this->processVersions();
        $processTypes = $graphs->pluck('full_namespace')->sort()->toArray();
        $complexityScore = number_format($graphs->pluck('complexity_score')
                ->sum() / ($graphs->count() === 0 ? 1 : $graphs->count()), 1);

        // Save current version, published date, changelog and complexity score.
        $this->update([
            'version' => $version,
            'published_at' => Carbon::now(),
            'changelog' => $changelog,
            'complexity_score' => $complexityScore,
            // In the published version, save the concrete version numbers.
            'data' => array_merge($this->data, ['process_types' => $processTypes])
        ]);

        $this->refresh();

        /* @var SolutionVersion $newVersion */
        $newVersion = $this->solution->versions()->save($this->replicate([
            'changelog',
            'published_at',
            'updated_at',
            'created_at'
        ]));
        $newVersion->update([
            'version' => 'develop',
            // In the new "In development" version keep versions as is.
            // They might have "latest" versions, wo we keep those references
            'data' => $oldData
        ]);

        // Set latest version and relations to latest solution version
        // and latest published version ($solution->latestVersion, $solution->latestPublishedVersion)
        $this->solution->update([
            'latest_version' => 'develop',
            'latest_version_id' => $newVersion->id,
            'latest_published_version_id' => $this->id
        ]);

        return $newVersion;
    }

    /**
     * Gibt eine bestimmte Version der Solutions anhand eines ganzen Namespaces mit Version zurück.
     * @param $fullNamespaceWithVersion
     * @param bool $published Flagge ob die Version publiziert sein muss. Bei "latest" with somit die aktuellste,
     * publizierte Version zurückgegeben.
     * @return Model|null
     */
    public static function whereFullNamespaceWithVersion($fullNamespaceWithVersion, bool $published = false) {
        $fullNamespace = explode('@', $fullNamespaceWithVersion)[0];
        $version = explode('@', $fullNamespaceWithVersion)[1] ?? 'latest';

        /* @var Solution $solution */
        $solution = Process::whereFullNamespace($fullNamespace)->with('versions')->first();

        return $solution?->version($version, $published);
    }

    /**
     * Gibt den kompletten Namespace mit Version zurück.
     * @return Attribute
     */
    protected function fullNamespace(): Attribute {
        return Attribute::make(get: fn(mixed $value, array $attributes) => $this->solution->full_namespace . '@' . $attributes['version']);
    }

    /**
     * Setzt das Publikationsdatum.
     */
    public function markAsPublished() {
        $this->update(['published_at' => Carbon::now()]);
        $this->fireModelEvent('published', false);
    }

    /**
     * Berechnet die Complexity Score und speichert diese ab.
     * @return void
     */
    public function updateComplexityScore(): void {
        // @Todo Durchschnitt der Abhängigkeiten.
        $this->update([
            'complexity_score' => 0.0
        ]);
    }

    /**
     * Gibt kumuliertes Array der Abhängigkeiten aller Graphen zurück.
     * @return array
     * @throws FileNotFoundException
     */
    public function dependencies(): array {
        $graphs = $this->processVersions();
        $dependencies = $graphs->reduce(fn($carry, ProcessVersion $processVersion) => array_merge_recursive($carry, $processVersion->dependencies()), [
            'process_types' => $this->data['process_types'] ?? []
        ]);

        return remove_duplicates_from_dependencies($dependencies);
    }

    /**
     * Gibt ein Array der Prozesstypen (Ganzer Namespace mit Version) aus den Abhängigkeiten zurück.
     * @return array
     */
    public function processTypes(): array {
        return $this->data['process_types'] ?? [];
    }


    /**
     * Gibt die Dateinamen der exportieren Graphen zurück.
     * @return Collection
     * @throws FileNotFoundException
     */
    public function dependentProcessFileNames(): Collection {
        $fullNamespacesWithVersion = $this->dependencies()['process_types'] ?? [];

        return collect($fullNamespacesWithVersion)->map(fn(string $item) => namespace_to_definition_export_file_name($item));
    }

    /**
     * Gibt die Main-Demo zurück.
     * @return Demo|null
     */
    public function mainDemo(): Demo|null {
        return $this->demos()->where('main', '=', true)->first();
    }

    /**
     * Flagge, ob ein Benutzer oder eine Organisation alle Prozess-Lizenzen zu dieser Version hat.
     * @param User|Organisation $licenseOwner
     * @return bool
     */
    public function licenseOwnerHasAllProcessLicenses(User|Organisation $licenseOwner): bool {
        $processNamespaces = collect($this->processTypes())->map(function (string $ns) {
            return namespace_parts($ns)['namespace'] . '/' . namespace_parts($ns)['identifier'];
        })->unique();

        return $licenseOwner->processLicenses->pluck('resource.full_namespace')
                ->intersect($processNamespaces)
                ->count() === $processNamespaces->count();
    }

}
