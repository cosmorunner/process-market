<?php /** @noinspection PhpUnused */

namespace App\Models;

use App\Enums\Settings;
use App\Http\Resources\ProcessFileDefinition;
use App\Http\Resources\SyntaxValues\GraphAuthorSyntaxValues;
use App\Interfaces\Cachable;
use App\Interfaces\Syncable;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Output;
use App\ProcessType\Template;
use App\Traits\UpdatesDependencies;
use App\Traits\UsesCache;
use App\Traits\UsesSynchronisation;
use App\Utils\ComplexityScore;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Graph
 * @property string $id
 * @property string $process_id
 * @property array $calculated
 * @property array|Definition $definition
 * @property string $version
 * @property string|null $changelog
 * @property array $demo_data
 * @property float $complexity_score
 * @property string $namespace
 * @property string $full_namespace
 * @property string $latest_namespace
 * @property string $develop_namespace
 * @property string|null $published_at
 * @property Support\Carbon|null $created_at
 * @property Support\Carbon|null $updated_at
 * @property User|null $updated_by
 * @property User|null $published_by
 * @property-read array $dependencies Only available when query selects "definition->dependencies as dependencies".
 * @property-read string $history_head
 * @property-read ProcessVersionHistory $historyHead
 * @property-read Process $process
 * @property-read \Illuminate\Database\Eloquent\Collection|ProcessVersionHistory[] $previousHistory
 * @property-read \Illuminate\Database\Eloquent\Collection|ProcessVersionHistory[] $succeedingHistory
 * @property-read \Illuminate\Database\Eloquent\Collection|Simulation[] $simulations
 * @property-read \Illuminate\Database\Eloquent\Collection|Synchronization[] $synchronizations
 * @property-read \Illuminate\Database\Eloquent\Collection|Environment[] $environments
 * @method static Builder|ProcessVersion newModelQuery()
 * @method static Builder|ProcessVersion newQuery()
 * @method static Builder|ProcessVersion query()
 * @method static Builder published()
 * @method static Builder|ProcessVersion whereCalculated($value)
 * @method static Builder|ProcessVersion whereCreatedAt($value)
 * @method static Builder|ProcessVersion whereDefinition($value)
 * @method static Builder|ProcessVersion whereId($value)
 * @method static Builder|ProcessVersion whereProcessId($value)
 * @method static Builder|ProcessVersion whereUpdatedAt($value)
 * @method static Builder|ProcessVersion whereVersion($value)
 * @method static Builder|ProcessVersion whereFullNamespace($value)
 * @method static Builder|ProcessVersion wherePublishedAt($value)
 * @method static Builder|ProcessVersion whereChangelog($value)
 * @method static Builder|ProcessVersion create($value)
 */
class ProcessVersion extends Model implements Syncable, Cachable {

    use HasUuids, HasFactory, UsesSynchronisation, UsesCache, UpdatesDependencies;

    protected $guarded = [];

    protected $casts = [
        'calculated' => 'array',
        'definition' => 'array',
        'demo_data' => 'array',
        'published_at' => 'date',
        'dependencies' => 'array'
    ];

    /**
     * Own Model events.
     * @var string[]
     */
    protected $observables = ['published', 'rolledback'];

    /**
     * @return BelongsTo
     */
    public function process() {
        return $this->belongsTo(Process::class);
    }

    /**
     * Simulations of the graph.
     * @return HasMany
     */
    public function simulations() {
        return $this->hasMany(Simulation::class);
    }

    /**
     * Simulation environments of the graph.
     * @return HasMany
     */
    public function environments() {
        return $this->hasMany(Environment::class);
    }

    /**
     * User that updated the process version.
     * @return HasOne
     */
    public function updatedUser() {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * User that published the process version.
     * @return HasOne
     */
    public function publisher() {
        return $this->hasOne(User::class, 'id', 'published_by');
    }

    /**
     * Process version history.
     * @return HasMany
     */
    public function history() {
        return $this->hasMany(ProcessVersionHistory::class, 'process_version_id', 'id');
    }

    /**
     * Get process version history before (older than current head).
     * @return HasMany
     */
    public function previousHistory() {
        return $this->hasMany(ProcessVersionHistory::class, 'process_version_id', 'id')
            ->where('created_at', '<', $this->historyHead?->created_at ?? now());
    }

    /**
     * Get process version history after its current head (younger then current head).
     * @return HasMany
     */
    public function succeedingHistory() {
        return $this->hasMany(ProcessVersionHistory::class, 'process_version_id', 'id')
            ->where('created_at', '>', $this->historyHead?->created_at ?? now());
    }

    /**
     * Flag if there is a history older than current head.
     * @return bool
     */
    public function hasPreviousHistory() {
        if (!$this->history_head) {
            return false;
        }

        return (bool) $this->previousHistory()->count();
    }

    /**
     * Flag if there is a history younger than current head.
     * @return bool
     */
    public function hasSucceedingHistory() {
        if (!$this->history_head) {
            return false;
        }

        return (bool) $this->succeedingHistory()->count();
    }

    /**
     * Removes the history from the process version.
     * @param bool $initialize Flag if a new history should be initialzed.
     * @return void
     */
    public function clearHistory(bool $initialize = false) {
        $this->history()->delete();
        $this->update(['history_head' => null]);

        if ($initialize) {
            $this->history()->save(ProcessVersionHistory::makeInitial($this));
        }
    }

    /**
     * Get history head.
     * @return HasOne
     */
    public function historyHead() {
        return $this->hasOne(ProcessVersionHistory::class, 'id', 'history_head');
    }

    /**
     * Flagge ob die ProzessVersion eine Standard-Simulationsumgebung hat.
     */
    public function hasDefaultEnvironment() {
        return $this->environments()->where('default', '=', true)->count(['id']) > 0;
    }

    /**
     * Gibt die Standard-Simulationsumgebung zurück.
     * @return Model|null
     */
    public function defaultEnvironment() {
        return $this->environments()->where('default', '=', true)->first();
    }

    /**
     * Prüft ob der Graf eine laufende Simulation hat.
     */
    public function hasRunningSimulations() {
        return $this->simulations()->where('finished_at', '=', null)->exists();
    }

    /**
     * Prüft ob der Graph eine laufende Simulation hat.
     */
    public function runningSimulations() {
        return $this->simulations()->where('finished_at', '=', null);
    }

    /**
     * Gibt eine laufende Simulation des Graphen von dem eingeloggten Benutzer zurück.
     * @return Simulation|null
     */
    public function runningUserSimulation(): Simulation|null {
        /* @var User $user */
        $user = auth()->user();

        return $user->runningUserSimulations($this);
    }

    /**
     * Gibt die Resource einer laufenden Simulation des Graphen zurück.
     * @return \App\Http\Resources\Simulation|null
     */
    public function runningUserSimulationResource() {
        $simulation = $this->runningUserSimulation();

        if ($simulation instanceof Simulation) {
            return $simulation->asResource();
        }

        return null;
    }

    /**
     * Scope für publizierte Graphen.
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished($query) {
        return $query->where('published_at', '!=', null);
    }

    /**
     * Returns all dependencies (direct and indirect) of the graph.
     * Reads from the "..._dependencies.json" file.
     * @return array
     * @throws FileNotFoundException
     */
    public function dependencies(): array {
        $path = $this->dependenciesExportFilePath();

        if (!Storage::exists($path)) {
            $result = $this->exportDependencies();

            if (!$result) {
                return [];
            }
        }

        return json_decode(Storage::get($path), true);
    }

    /**
     * Gibt eine Collection aller abhängigen Prozessentypen (process_types) aus der "...dependencies.json"
     * Datei zurück.
     * @return Collection
     * @throws FileNotFoundException
     */
    public function processTypeDependencies(): Collection {
        return collect($this->dependencies()['process_types'] ?? []);
    }

    /**
     * Gibt ein Array aller Pfade zu den JSON-Dateinamen der zuvor exportierten, abhängigen Prozesse zurück (im "process_types" Ordner).
     * Inklusive der indirekten Abhängigkeiten.
     * @return Collection Z.B. ["process_types/allisa_issue@1-0-0.json", "process_types/allisa_demo@1-0-0.json"]
     * @throws FileNotFoundException
     */
    public function dependentProcessFileNames(): Collection {
        $processTypes = collect($this->dependencies()['process_types'] ?? []);

        // Zu Datei-Pfaden mappen.
        return $processTypes->map(fn(string $item) => namespace_to_definition_export_file_name($item));
    }

    /**
     * Gibt die Prozesstyp-Definition als Definition-Object zurück.
     * @return Attribute
     */
    protected function definition(): Attribute {
        return Attribute::make(get: fn(mixed $value) => new Definition($this->castAttribute('definition', $value)));
    }

    /**
     * Gibt die Prozesstyp-Definition als Array zurück.
     * @return array
     */
    public function getRawDefintion() {
        return $this->castAttribute('definition', $this->getRawOriginal('definition'));
    }

    /**
     * Flag, if process version has been published.
     * @return bool
     */
    public function isPublished() {
        return $this->published_at !== null;
    }

    /**
     * Flagge, ob sich die Prozess-Version noch in der Entwicklung befindet.
     * @return bool
     */
    public function isInDevelopment() {
        return !$this->isPublished();
    }

    /**
     * Checks whether the process version could be used as a process identity.
     * @return bool
     */
    public function isValidIdentityVersion(): bool {
        $outputs = $this->definition->outputs->map(fn(Output $output) => $output->name);

        if (($outputs->contains('first_name') && $outputs->contains('last_name') && $outputs->contains('user') && $outputs->contains('email')) || ($outputs->contains('vorname') && $outputs->contains('nachname') && $outputs->contains('benutzer') && $outputs->contains('email'))) {
            return true;
        }

        return false;
    }

    /**
     * Flag, whether this process version is the latest published version of the process.
     * @return bool
     */
    public function isLatestPublishedVersion(): bool {
        return $this->process->latest_published_version_id === $this->id;
    }

    /**
     * Publiziert die ProzessVersion, indem diese dupliziert wird und als "published" markiert wird
     * die Graph-Id im Prozess eingetragen wird.
     * @param string $version
     * @param string|null $changelog
     * @return void
     */
    public function publish(string $version, ?string $changelog = null) {
        // Update in definition and process version table
        $definition = $this->definition->toArray();
        $definition['version'] = $version;

        // Save current version, published date, changelog and complexity score.
        $this->update([
            'version' => $version,
            'published_at' => now(),
            'published_by' => auth()->user()->id,
            'changelog' => $changelog,
            'definition' => $definition,
            'complexity_score' => ComplexityScore::calculcate($this->definition),
            'full_namespace' => $this->definition->namespace . '/' . $this->definition->identifier . '@' . $version,
            'history_head' => null
        ]);

        $this->fireModelEvent('published', false);
    }

    /**
     * Rolls back this version to another version by copying the definition and graph to it and deleting the history.
     * @param ProcessVersion $processVersion
     * @return void
     */
    public function rollbackTo(ProcessVersion $processVersion) {
        // Copy definition and version to version "in development", update version number in definition with latest version.
        $definition = $processVersion->definition;
        $definition->version = $this->version; // Sets the version to "develop".

        $this->update([
            'definition' => $definition->toArray(),
            'calculated' => $processVersion->calculated
        ]);

        $this->fireModelEvent('rolledback', false);
    }

    /**
     * Create a new "develop" version from this process version and set it as "latest_version"
     * @return ProcessVersion
     */
    public function createDevelopVersion(): ProcessVersion {
        /* @var ProcessVersion $processVersion */
        $processVersion = $this->process->versions()->save($this->replicate([
            'changelog',
            'published_at',
            'updated_at',
            'created_at',
            'history_head'
        ]));

        $fullNamespace = $this->definition->namespace . '/' . $this->definition->identifier . '@' . 'develop';

        $processVersion->update([
            'version' => 'develop',
            'definition->version' => 'develop',
            'full_namespace' => $fullNamespace
        ]);

        // Latest version and relations to latest process version and latest published version.
        // process version ($process->latestVersion, $process->latestPublishedVersion).
        $this->process->update([
            'latest_version' => 'develop',
            'latest_version_id' => $processVersion->id,
            'latest_published_version_id' => $this->id
        ]);

        return $processVersion;
    }

    /**
     * Returns a specific version of the graph using an entire namespace with version.
     * @param $fullNamespaceWithVersion
     * @param bool $published Flag whether the version must be published.
     * With "latest" as the version the most current, published version is returned.
     * @return ProcessVersion|null
     */
    public static function findByFullNamespace($fullNamespaceWithVersion, bool $published = false): ProcessVersion|null {
        $parts = namespace_parts($fullNamespaceWithVersion);
        $process = Process::whereFullNamespace($parts['namespace'] . '/' . $parts['identifier'])->with('versions')->first();
        $processVersion = $process?->version($parts['version'], $published);
        $processVersion?->setRelation('process', $process);

        return $processVersion;
    }

    /**
     * Exports the process version definition as a JSON string.
     * @param string|null $asVersion Export the definition with a specific version number other than the original one.
     * @return bool|string Path to json file, FALSE if definition could not be exported.
     */
    public function exportDefinition(string $asVersion = null): string|false {
        $path = $this->definitionExportFilePath($asVersion);
        Storage::put($path, json_encode(new ProcessFileDefinition($this)));

        if (!Storage::exists($path)) {
            return false;
        }

        return $path;
    }

    /**
     * Flag if the process version definition file exists in the storage.
     * E.g. "robert_test@1.0.0.json"
     * @param string|null $version A version number. If empty, the original process version number is used.
     * @return bool
     */
    public function definitionExportFileExists(string|null $version = null): bool {
        return Storage::exists($this->definitionExportFilePath($version));
    }

    /**
     * Returns the export filename of the dependencies of an exported graph generated from the namespace, e.g. "allisa/demo@1.0.0". This will be used for
     * the graph export.
     * @param string|null $version A version number. If empty, the original process version number is used.
     * of the original process version.
     * @return string
     */
    public function definitionExportFileName(string|null $version = null): string {
        return namespace_to_definition_export_file_name($this->full_namespace, $version);
    }

    /**
     * Storage path to exported process version definition.
     * @param string|null $version A version number. If empty, the original process version number is used.
     * @return string
     */
    public function definitionExportFilePath(string|null $version = null): string {
        return config('app.process_types_dir') . '/' . $this->definitionExportFileName($version);
    }

    /**
     * Determines all direct and indirect dependencies (dependencies of dependencies) of the process version and
     * writes these dependencies to a JSON file and also exports the definitions of the dependencies.
     * @param string|null $asVersion Export the dependencies with a specific version number other than the original one.
     * of the concrete version, e.g. "allisa_demo@1.0.0_dependencies.json".
     * @return bool|string
     * @throws FileNotFoundException
     */
    public function exportDependencies(string $asVersion = null): string|false {
        $path = $this->dependenciesExportFilePath($asVersion);
        $dependencies = self::recursiveDependenciesSearch($this->definition, Definition::EMPTY_DEPENDENCIES);
        $dependencies = array_merge_recursive($this->definition->dependencies, $dependencies);
        $dependencies = remove_duplicates_from_dependencies($dependencies);

        Storage::put($path, json_encode($dependencies));

        // Make sure that the dependent processes are exported as well.
        foreach ($this->processTypeDependencies() as $processTypeDependency) {
            $processVersion = ProcessVersion::findByFullNamespace($processTypeDependency, true);
            $dependencyVersion = explode('@', $processTypeDependency)[1] ?? 'latest';

            if ($processVersion && !$processVersion->definitionExportFileExists()) {
                $processVersion->exportDefinition($dependencyVersion);
            }
        }

        if (!Storage::exists($path)) {
            return false;
        }

        return $path;
    }

    /**
     * Flag if the dependency file exists in the storage.
     * E.g. "robert_test@1.0.0_dependencies.json"
     * @return bool
     */
    public function dependenciesExportFileExists(): bool {
        return Storage::exists($this->dependenciesExportFilePath());
    }

    /**
     * Returns the export filename of the dependencies of a process version generated from the namespace, e.g. "allisa/demo@1.0.0". This will be used for
     * the graph export.
     * @param string|null $version Get the file path with a specific version number as file name, e.g. "allisa_demo@latest_dependencies.json".
     * @return string
     */
    public function dependenciesExportFileName(string|null $version = null): string {
        $parts = namespace_parts($this->full_namespace);
        $namespace = $version ? $parts['namespace'] . '/' . $parts['identifier'] . '@' . $version : $this->full_namespace;

        return namespace_to_dependencies_export_file_name($namespace);
    }

    /**
     * Gibt den vollständigen Pfad zur Abhängigkeiten-Datei des Graphen zurück.
     * @param string|null $asVersion Get the file path with "latest" as file name, e.g. "allisa_demo@latest_dependencies.json".
     * @return string
     */
    public function dependenciesExportFilePath(string|null $asVersion = null) {
        return config('app.process_types_dir') . '/' . $this->dependenciesExportFileName($asVersion);
    }

    /**
     * Gibt den "full_namespace" mit "latest" als Version zurück.
     * @return Attribute
     */
    protected function latestNamespace(): Attribute {
        return Attribute::make(get: function (mixed $value, array $attributes) {
            $parts = namespace_parts($attributes['full_namespace']);

            return $parts['namespace'] . '/' . $parts['identifier'] . '@latest';
        });
    }

    /**
     * Gibt den "full_namespace" mit "develop" als Version zurück.
     * @return Attribute
     */
    protected function developNamespace(): Attribute {
        return Attribute::make(get: function (mixed $value, array $attributes) {
            $parts = namespace_parts($attributes['full_namespace']);

            return $parts['namespace'] . '/' . $parts['identifier'] . '@develop';
        });
    }

    /**
     * Returns the namespace and identifier.
     * @return Attribute
     */
    protected function namespace(): Attribute {
        return Attribute::make(get: function (mixed $value, array $attributes) {
            $parts = namespace_parts($attributes['full_namespace']);

            return $parts['namespace'] . '/' . $parts['identifier'];
        });
    }

    /**
     * Berechnet die Complexity Score und speichert diese ab.
     * @return void
     */
    public function updateComplexityScore() {
        $this->update([
            'complexity_score' => ComplexityScore::calculcate($this->definition)
        ]);
    }

    /**
     * Aktualisiert die Abhängigkeiten der Definition.
     * @return ProcessVersion
     */
    public function updateDependencies(): ProcessVersion {
        $this->update([
            'definition->dependencies' => self::updateDefinitionDependencies($this->definition, $this->environments)->dependencies
        ]);

        return $this;
    }

    /**
     * Ermittelt anhand eines Arrays von Prozess Abhängigkeiten alle indirekten Abhängigkeiten
     * @param Definition $definition
     * @param array $dependencies
     * @return array
     */
    public static function recursiveDependenciesSearch(Definition $definition, array $dependencies): array {
        foreach ($definition->processTypeDependencies() as $fullNamespaceWithVersion) {
            if (in_array($fullNamespaceWithVersion, $dependencies['process_types'])) {
                continue;
            }

            if (!Definition::validNamespace($fullNamespaceWithVersion)) {
                continue;
            }

            $processVersion = ProcessVersion::findByFullNamespace($fullNamespaceWithVersion);

            if (!$processVersion) {
                continue;
            }

            // Gefundene Version ablegen, sodass beim rekursiven Aufruf nicht erneut mit dem Namespace gesucht wird.
            $dependencies['process_types'][] = $fullNamespaceWithVersion;
            $otherDependencies = $processVersion->definition->dependencies;
            unset($otherDependencies['process_types']);

            // Andere Abhängigkeiten außer Prozesstypen mergen.
            $dependencies = array_merge_recursive($dependencies, $otherDependencies);
            $dependencies = array_merge_recursive($dependencies, self::recursiveDependenciesSearch($processVersion->definition, $dependencies));
        }

        foreach ($dependencies as $key => $items) {
            $unique = is_array($items) && Arr::isList($items) ? array_unique($items) : $items;
            $dependencies[$key] = array_values($unique);
        }

        return $dependencies;
    }

    /**
     * @param array $parts
     * @param ActionType|null $actionType
     * @param string|null $search Optional search parameter
     * @return array|array[]
     */
    public function syntaxValues(array $parts, ActionType $actionType = null, string $search = null) {
        return (new GraphAuthorSyntaxValues($this))->additional([
            'actionType' => $actionType,
            'syntaxParts' => $parts['syntax_parts'],
            'pipeParts' => $parts['pipe_parts'],
            'search' => $search
        ])->toArray(request());
    }

    /**
     * Returns the demo environments for the user. Depending on access,
     * the private demo environments are also returned.
     * @return Collection
     */
    public function userEnvironments() {
        /* @var User $user */
        $user = auth()->user();
        $enabledPrivate = $this->process->enabledPrivateEnvironments($user);

        return $enabledPrivate ? $this->environments : $this->environments()->where('public', '=', true)->get();
    }

    /**
     * For each template, preview datasets exists. This method
     * checks the mapping values and updates the corresponding datasets
     * by adding/removing/changing dataset fields based on updated mapping values.
     * @return void
     */
    public function syncTemplatePreviewDatasets() {
        $datasets = Setting::retrieve(Settings::TemplatePreviewDatasets->value, [], $this);
        $templates = $this->definition->templates;

        /* @var Template $template */
        foreach ($templates as $template) {
            // If none exists, the default preview dataset is created.
            $templateDatasets = $datasets[$template->id] ?? [$template->defaultPreviewDataset()];

            // Loop through each dataset to eventually update dataset.
            foreach ($templateDatasets as $index => $dataset) {
                $datasetValues = $dataset['values'] ?? [];

                // Loop through each mapping value and add/remove/update dataset value if necessary.
                foreach ($template->mapping as $name => $item) {
                    $type = $item['type'];

                    // Mapping value does not exist in dataset.
                    if (!array_key_exists($name, $datasetValues)) {
                        $datasetValues[$name] = $template->defaultPreviewDatasetValueBasedOnMappingType($type);
                    }

                    // Identify "corrupt" dataset values by comparing the type of the mapping value and its value to the dataset value.
                    if ($type === Template::MAPPING_TYPE_STRING) {
                        if (!is_string($datasetValues[$name])) {
                            $datasetValues[$name] = $template->defaultPreviewDatasetValueBasedOnMappingType($type);
                        }
                    }

                    if ($type === Template::MAPPING_TYPE_ARRAY || $type === Template::MAPPING_TYPE_LIST_CONFING) {
                        if (!is_array($datasetValues[$name])) {
                            $datasetValues[$name] = $template->defaultPreviewDatasetValueBasedOnMappingType($type);
                        }
                    }
                }

                // Finally, we remove any dataset values, that do not exist as a mapping (anymore) or global variable.
                $keys = array_unique([...array_keys($template->mapping), ...Template::GLOBAL_VARIABLES]);

                // For mustache list column templates, there is only the "js" mapping key. The preview values for the list row is saved in the
                // dataset with the "process_list" key.
                if ($template->type === Template::TYPE_MUSTACHE_LIST_COLUMN) {
                    $keys[] = 'process_list';
                }

                $datasetValues = array_reduce($keys, function ($carry, $name) use ($datasetValues) {
                    if (array_key_exists($name, $datasetValues)) {
                        return [...$carry, ...[$name => $datasetValues[$name]]];
                    }
                    else {
                        return $carry;
                    }
                }, []);

                $dataset['values'] = $datasetValues;
                $templateDatasets[$index] = $dataset;
            }

            $datasets[$template->id] = $templateDatasets;
        }

        // Reduce datasets to those of templates that exist.
        $datasets = $templates->reduce(function ($carry, $template) use ($datasets) {
            /** @noinspection PhpUnpackedArgumentTypeMismatchInspection */
            return [...$carry, ...[$template->id => $datasets[$template->id]]];
        }, []);

        Setting::upsertSetting(Settings::TemplatePreviewDatasets->value, $datasets, $this);
    }

    /**
     * Deletes the preview datasets.
     * @return void
     */
    public function deletePreviewDatasets() {
        Setting::deleteSetting(Settings::TemplatePreviewDatasets->value, $this);
    }
}