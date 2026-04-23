<?php

namespace App\Models;

use Database\Factories\DemoFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\Demo
 * @property int $id
 * @property string|null $user_id
 * @property string|null $solution_id
 * @property string|null $organisation_id
 * @property string|null $solution_version_id
 * @property string|null $license_id
 * @property string|null $token
 * @property string|null $allisa_user_id
 * @property string|null $finished_at
 * @property bool $main
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read Solution|null $solution
 * @property-read Organisation|null $organisation
 * @property-read SolutionVersion|null $solutionVersion
 * @property-read License|null $license
 * @method static DemoFactory factory(...$parameters)
 * @method static Builder|Demo newModelQuery()
 * @method static Builder|Demo newQuery()
 * @method static Builder|Demo query()
 * @method static Builder|Demo whereAllisaUserId($value)
 * @method static Builder|Demo whereCreatedAt($value)
 * @method static Builder|Demo whereDatabaseFilename($value)
 * @method static Builder|Demo whereFinishedAt($value)
 * @method static Builder|Demo whereId($value)
 * @method static Builder|Demo whereSolutionId($value)
 * @method static Builder|Demo whereSolutionVersionId($value)
 * @method static Builder|Demo whereToken($value)
 * @method static Builder|Demo whereUpdatedAt($value)
 * @method static Builder|Demo whereUserId($value)
 * @method static Builder|Demo whereOrganisationId($value)
 * @method static Builder|Demo create($value)
 * @mixin Eloquent
 */
class Demo extends Model {

    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'finished_at' => 'date',
        'main' => 'bool'
    ];

    /**
     * Lösung der Demo.
     * @return BelongsTo
     */
    public function solution() {
        return $this->belongsTo(Solution::class);
    }

    /**
     * Ersteller der Demo.
     * @return BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Organisation des Prozesses/Lizenz der Demo.
     * @return BelongsTo
     */
    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Lösung-Version der Demo.
     * @return BelongsTo
     */
    public function solutionVersion() {
        return $this->belongsTo(SolutionVersion::class);
    }

    /**
     * Lizenz die für das Starten der Demo genutzt wurde.
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
        return !$this->isFinished();
    }

    /**
     * Flagge, ob die Demo eine Admin-Demo ist.
     * @return bool
     */
    public function isAdminDemo() {
        return $this->main == true;
    }

    /**
     * Flagge, ob die Demo eine Benutzer-Demo ist.
     * @return bool
     */
    public function isUserDemo() {
        return !$this->isAdminDemo();
    }

    /**
     * Erstellt für eine Main-Demo für eine Lösungsversion.
     * @param SolutionVersion $solutionVersion
     * @param string|null $id Optionale Angabe einer Id.
     * @return Demo|Model
     */
    public static function createMainDemo(SolutionVersion $solutionVersion, string $id = null) {
        return Demo::create([
            'id' => $id ?? Str::uuid()->toString(),
            'user_id' => null,
            'solution_id' => $solutionVersion->solution->id,
            'main' => true,
            'solution_version_id' => $solutionVersion->id,
            'allisa_user_id' => config('allisa.live_demo.admin_id')
        ]);
    }
}