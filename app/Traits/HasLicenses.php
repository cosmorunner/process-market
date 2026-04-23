<?php

namespace App\Traits;

use App\Models\License;
use App\Models\Process;
use App\Models\ProcessLicense;
use App\Models\Solution;
use App\Models\SolutionLicense;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Wird Models gegeben, welche Lizenzen besitzen können.
 */
trait HasLicenses {

    /**
     * Lizenzen, die der Benutzer erworben hat.
     * @return MorphMany
     */
    public function processLicenses(): MorphMany {
        return $this->morphMany(ProcessLicense::class, 'owner')->with(['resource'])->whereHas('resource');
    }

    /**
     * Lizenzen, die der Benutzer erworben hat.
     * @return MorphMany
     * @noinspection PhpUnused
     */
    public function solutionLicenses(): MorphMany {
        return $this->morphMany(SolutionLicense::class, 'owner')->with(['resource'])->whereHas('resource');
    }

    /**
     * Gibt einen Builder zurück, bei dem nur die Open-Source Prozess-Lizenzen selektiert werden.
     * @return MorphMany
     */
    public function openSourceProcessLicenses() {
        return $this->processLicenses()->where('level', '=', 'open-source');
    }

    /**
     * Flagge, ob ein Benutzer oder eine Organisation bereits eine bestimmte Lizenz zu einem Prozess oder zu
     * einer Lösung besitzt. Optional kann auf ein Lizenz-Level geprüft werden.
     * @param Process|Solution $resource
     * @param string|null $level
     * @return bool
     */
    public function hasLicense(Process|Solution $resource, string $level = null): bool {
        $where = [
            'resource_id' => $resource->id,
            'resource_type' => $resource::class,
        ];

        if ($level) {
            $where['level'] = $level;
        }

        return $this->licenses()->where($where)->exists();
    }

    /**
     * Lizenzen, die der Benutzer erworben hat.
     * @return MorphMany
     */
    public function licenses(): MorphMany {
        return $this->morphMany(License::class, 'owner');
    }

    /**
     * Alle Prozesse der Prozess-Lizenzen
     * @return Collection
     */
    public function licensedProcesses(): Collection {
        return $this->processLicenses()->with([
            'resource',
            'resource.latestPublishedVersion',
            'resource.latestPublishedVersion.process'
        ])->get()->pluck('resource');
    }

    /**
     * Gibt von jeder Prozess-Lizenz die aktuellste, publizierte Version zurück.
     * @return Collection
     */
    public function latestLicencedProcessVersions(): Collection {
        return $this->licensedProcesses()->pluck('latestPublishedVersion');
    }

}
