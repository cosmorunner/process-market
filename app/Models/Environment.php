<?php

namespace App\Models;

use App\Environment\Blueprint;
use App\Environment\Process as EnvironmentProcess;
use App\Environment\Relation;
use App\Environment\User as EnvironmentUser;
use App\Loaders\PipeLoader;
use App\ProcessType\ActionType;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Repräsentiert eine Umgebung für eine Simulation die in der Allisa-Instanz erzeugt wird.
 * Class Environment
 * @mixin Eloquent
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $process_version_id
 * @property string $initial_action_type_id
 * @property string $query_context
 * @property string $default_user
 * @property bool $default
 * @property bool $public
 * @property Blueprint $blueprint
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProcessVersion $processVersion
 * @method static Builder|Environment newModelQuery()
 * @method static Builder|Environment newQuery()
 * @method static Builder|Environment query()
 * @method static Builder|Environment whereBlueprint($value)
 * @method static Builder|Environment whereCreatedAt($value)
 * @method static Builder|Environment whereDescription($value)
 * @method static Builder|Environment whereProcessVersionId($value)
 * @method static Builder|Environment whereId($value)
 * @method static Builder|Environment whereName($value)
 * @method static Builder|Environment whereUpdatedAt($value)
 * @method static Builder|Environment whereDefault($value)
 * @method static Builder|Environment create($value)
 */
class Environment extends Model {

    use HasUuids, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'blueprint' => 'array',
        'public' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function processVersion() {
        return $this->belongsTo(ProcessVersion::class);
    }

    /**
     * Gibt das blueprint-Attribut als Model zurück.
     * @return Attribute
     */
    protected function blueprint(): Attribute {
        return Attribute::make(get: fn(mixed $value) => Blueprint::make($this->castAttribute('blueprint', $value)));
    }

    /**
     * Update a Blueprint property
     * @param string $property
     * @param Collection $collection
     * @return void
     */
    public function updateBlueprint(string $property, Collection $collection) {
        if (!property_exists($this->blueprint, $property)) {
            return;
        }

        $blueprint = $this->blueprint;
        $blueprint->$property = $collection;

        $this->setAttribute('blueprint', $blueprint->toArray());
    }

    /**
     * Gibt die Prozesstyp-Definition als Array zurück.
     * @return array
     */
    public function getRawBlueprint() {
        return $this->castAttribute('blueprint', $this->getRawOriginal('blueprint'));
    }

    /**
     * Gibt den initialen Aktionstyp zurück, sofern einer angegeben wurde.
     * @return ActionType|null
     */
    public function initialActionType() {
        return $this->processVersion->definition->actionType($this->initial_action_type_id);
    }

    /**
     * Flagge ob das Environment das Standard-Environment ist.
     * @return bool
     */
    public function isDefault() {
        return $this->default;
    }

    /**
     * Exports the processes used in the environment to .json process files, so that they can be
     * can be imported when starting a demo.
     * @param string $excludeNamespace Ignore the namespace of the demo process because it will be imported anyway.
     * @return Collection File names of the exported processes.
     */
    public function exportProcesses(string $excludeNamespace = ''): Collection {
        // Processes of the process identity of the users.
        $processIdentityTypes = $this->blueprint->users->map(fn(EnvironmentUser $user) => $user->identity_process_type);

        // Dependent processes.
        $processTypes = $this->blueprint->processes->map(fn(EnvironmentProcess $process) => $process->process_type);

        // Processes of the relations of the environment blueprint.
        $processTypesOfRelationtypes = $this->blueprint->relations->map(fn(Relation $relation) => PipeLoader::getFullNamespaceWithVersion($relation->relation_type));

        // Remove duplicates and remove the namespace of the demo process.
        $namespaces = collect([...$processIdentityTypes, ...$processTypes, ...$processTypesOfRelationtypes])
            ->unique()
            ->filter(fn($ele) => is_string($ele) && strlen($ele) > 0 && $ele !== $excludeNamespace);

        // For each namespace the graph is searched for and exported if available and the filename is is returned.
        return $namespaces->map(function (string $fullNamespaceWithVersion) {
            $processVersion = ProcessVersion::findByFullNamespace($fullNamespaceWithVersion, true);

            if (!$processVersion instanceof ProcessVersion) {
                return null;
            }

            if ($processVersion->definitionExportFileExists()) {
                return $processVersion->definitionExportFileName();
            }

            return $processVersion->exportDefinition();
        })->filter(fn($ele) => is_string($ele));
    }
}
