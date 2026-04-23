<?php

namespace App\Models;

use App\Http\Resources\ProcessFileDefinition;
use App\Http\Resources\Simulation as SimulationResource;
use App\SimulationConnector;
use Eloquent;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\UnauthorizedException;
use Throwable;

/**
 * App\Models\Simulation
 * @property string $id
 * @property string $user_id
 * @property string|null $process_id
 * @property string|null $process_version_id
 * @property string|null $organisation_id
 * @property array $situation
 * @property array $active_actions
 * @property array $data
 * @property string|null $finished_at
 * @property string|null $context
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProcessVersion|null $processVersion
 * @property-read Organisation|null $organisation
 * @property-read Process|null $process
 * @method static Builder|Simulation newModelQuery()
 * @method static Builder|Simulation newQuery()
 * @method static Builder|Simulation query()
 * @method static Builder|Simulation whereActiveActions($value)
 * @method static Builder|Simulation whereCreatedAt($value)
 * @method static Builder|Simulation whereData($value)
 * @method static Builder|Simulation whereFinishedAt($value)
 * @method static Builder|Simulation whereProcessVersionId($value)
 * @method static Builder|Simulation whereId($value)
 * @method static Builder|Simulation whereProcessId($value)
 * @method static Builder|Simulation whereSituation($value)
 * @method static Builder|Simulation whereUpdatedAt($value)
 * @method static Builder|Simulation whereUserId($value)
 * @method static Builder|Simulation whereOrganisationId($value)
 * @mixin Eloquent
 * @property string|null $token
 * @property string|null $allisa_id
 * @property array|null $allisa_user_id
 * @property array|null $allisa_final_situation
 * @property array|null $allisa_final_data
 * @property array|null $allisa_action_history
 * @method static Builder|Simulation whereAllisaActionHistory($value)
 * @method static Builder|Simulation whereAllisaFinalData($value)
 * @method static Builder|Simulation whereAllisaFinalSituation($value)
 * @method static Builder|Simulation whereAllisaId($value)
 * @method static Builder|Simulation whereAllisaInitialData($value)
 * @method static Builder|Simulation whereToken($value)
 * @method static Builder|Simulation create($value)
 * @property mixed|null $allisa_situation
 * @property mixed|null $allisa_data
 * @method static Builder|Simulation whereAllisaData($value)
 * @method static Builder|Simulation whereAllisaSituation($value)
 * @property string|null $allisa_process_type_id
 * @method static Builder|Simulation whereAllisaProcessTypeId($value)
 * @method static Builder|Simulation whereAllisaRoleId($value)
 * @property string|null $environment_id
 * @property-read Environment|null $environment
 * @property-read User|null $user
 * @method static Builder|Simulation whereEnvironmentId($value)
 * @method static Builder|Simulation whereAllisaUserId($value)
 */
class Simulation extends Model {

    use HasUuids, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'finished_at' => 'date'
    ];

    /**
     * Prozess der Simulation.
     * @return BelongsTo
     */
    public function process() {
        return $this->belongsTo(Process::class);
    }

    /**
     * Ersteller der Simulation.
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Organisation des Prozesses/Lizenz der Simulation.
     * @return BelongsTo
     */
    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Prozess-Version der Simulation.
     * @return BelongsTo
     */
    public function processVersion() {
        return $this->belongsTo(ProcessVersion::class);
    }

    /**
     * Environment der Simulation.
     * @return BelongsTo
     */
    public function environment() {
        return $this->belongsTo(Environment::class);
    }

    /**
     * Lizenz die für das Starten der Simulation genutzt wurde.
     * @return BelongsTo
     */
    public function license() {
        return $this->belongsTo(License::class);
    }

    /**
     * Simulatin als abgeschlossen markieren.
     */
    public function markAsFinished() {
        $this->update(['finished_at' => now()]);
    }

    /**
     * Flagge ob eine Simulation bereits beendet wurde.
     * @return bool
     */
    public function isFinished() {
        return $this->finished_at !== null;
    }

    /**
     * Flagge ob eine Simulation noch läuft.
     * @return bool
     */
    public function isRunning() {
        return $this->finished_at === null;
    }

    /**
     * Gibt die Simulation als Resource für die "develop"-Ansicht (Vue App) zurück.
     * @return SimulationResource
     * @throws UnauthorizedException
     */
    public function asResource() {
        $http = new SimulationConnector($this);
        $error = null;
        $errorMessage = null;

        try {
            $process = $http->getProcess($this->allisa_id);
        }
        catch (ClientException $exception) {
            $process = [];
            $error = $exception->getResponse()->getStatusCode();
            $errorMessage = $error === Response::HTTP_FORBIDDEN ? 'Kein Zugriff auf den Prozess.' : 'Fehler beim Laden des Prozesses';
        }
        catch (Throwable) {
            $process = [];
            $error = Response::HTTP_INTERNAL_SERVER_ERROR;
            $errorMessage = 'Fehler beim Laden des Prozesses';
        }

        return (new SimulationResource($this))->additional([
            'process' => $process,
            'error' => $error,
            'error_message' => $errorMessage
        ]);
    }

    /**
     * Beended eine laufende Simulation.
     * @throws GuzzleException
     */
    public function finish() {
        if ($this->isFinished()) {
            return;
        }

        // Exportierten Prozesstyp löschen
        Storage::delete($this->definitionExportFilePath());

        try {
            $connector = new SimulationConnector($this);
            $connector->deleteAllisaSimulation();
            $this->markAsFinished();
        }
        catch (BadResponseException $exception) {
            report($exception);
        }
    }

    /**
     *
     * @return Organisation|User
     */
    public function origin(): Organisation|User {
        /* @var User $user */
        $user = auth()->user();

        return $this->organisation_id ? $this->organisation : $user;
    }

    /**
     * Erstellt für eine Prozess-Version und eine optionale Rolle eine Simulation.
     * @param ProcessVersion $processVersion
     * @param string|null $organisationId
     * @param string|null $licenseId
     * @return Simulation|Model
     */
    public static function createForProcessVersion(ProcessVersion $processVersion, string $organisationId = null, string $licenseId = null) {
        return Simulation::create([
            'user_id' => auth()->user()->id,
            'organisation_id' => $organisationId,
            'license_id' => $licenseId,
            'process_id' => $processVersion->process->id,
            'process_version_id' => $processVersion->id,
            'allisa_user_id' => config('allisa.simulation.user_id')
        ]);
    }

    /**
     * Exports the process version definition used in the simulation to a json file with the version number
     * of the process version being the simulation id.
     * @return bool|string
     */
    public function exportDefinition(): string|false {
        $path = $this->definitionExportFilePath();
        Storage::put($path, json_encode(new ProcessFileDefinition($this->processVersion)));

        if (!Storage::exists($path)) {
            return false;
        }

        return $path;
    }

    /**
     * Gibt den Pfad zur .JSON Prozesstyp-Datei zurück, welche für die Simulation genutzt wird.
     * @return string
     */
    public function definitionExportFilePath() {
        return config('app.process_types_dir') . '/' . $this->process->namespace . '_' . $this->process->identifier . '@' . $this->id . '.json';
    }

    /**
     * Exports the process version definition used in the simulation to a json file with the version number
     * of the process version being the simulation id.
     * @return bool|string
     */
    public function exportGraph(): string|false {
        $path = $this->graphExportFilePath();
        Storage::put($path, json_encode($this->processVersion->calculated));

        if (!Storage::exists($path)) {
            return false;
        }

        return $path;
    }

    /**
     * Gibt den Pfad zur .JSON Graph-Datei zurück, welche für die Simulation genutzt wird.
     * @return string
     */
    public function graphExportFilePath() {
        return config('app.process_types_dir') . '/' . $this->process->namespace . '_' . $this->process->identifier . '@' . $this->id . '_graph.json';
    }

}
