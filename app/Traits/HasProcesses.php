<?php

namespace App\Traits;

use App\Enums\Visibility;
use App\Models\Demo;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\Solution;
use App\Models\SolutionVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Gibt an, dass eine Entität Prozesse besitzten und bearbeiten kann. Z.B. "User" und "Organisation".
 */
trait HasProcesses {

    /**
     * Prozesse des Entität.
     * @return MorphMany|SoftDeletes
     */
    public function processes() {
        return $this->morphMany(Process::class, 'author');
    }

    /**
     * Solutions des Entität.
     * @return MorphMany|SoftDeletes
     */
    public function solutions() {
        return $this->morphMany(Solution::class, 'author');
    }

    /**
     * Demos der Entität.
     * @return HasMany
     */
    public function demos() {
        return $this->hasMany(Demo::class)->with(['solution', 'solutionVersion']);
    }

    /**
     * Simulationen der Entität
     * @return HasMany
     */
    public function simulations() {
        return $this->hasMany(Simulation::class)->with(['process', 'processVersion', 'environment']);
    }

    /**
     * Alle laufenden Demos der Entität.
     * @return HasMany
     */
    public function runningDemos() {
        return $this->demos()->where('finished_at', '=', null);
    }

    /**
     * Alle laufenden Simulationen der Entität.
     * @return HasMany
     */
    public function runningSimulations() {
        return $this->simulations()->where('finished_at', '=', null);
    }

    /**
     * Alle laufenden Demos der Entität von dem angemeldeten Benutzer.
     * @param SolutionVersion|null $solutionVersion Optionale Filterung nach einer bestimmten Lösungsversion.
     * @return Collection|Demo|Model|null
     */
    public function runningUserDemos(SolutionVersion $solutionVersion = null): Collection|Demo|Model|null {
        if ($solutionVersion) {
            $author = $solutionVersion->solution->author;

            return $this->runningDemos()->where([
                'solution_version_id' => $solutionVersion->id,
                'organisation_id' => $author instanceof Organisation ? $author->id : null
            ])->first();
        }
        else {
            return $this->runningDemos()->where([
                'organisation_id' => $this instanceof Organisation ? $this->id : null
            ])->get();
        }
    }

    /**
     * Alle laufenden Simulationen der Entität von dem angemeldeten Benutzer.
     * @param ProcessVersion|null $processVersion Optionale Filterung nach eine Simulation von einer Prozess-Version.
     * @return Collection|ProcessVersion|Model|null
     */
    public function runningUserSimulations(ProcessVersion $processVersion = null): Collection|Simulation|Model|null {
        if ($processVersion) {
            $author = $processVersion->process->author;

            return $this->runningSimulations()->where([
                'process_version_id' => $processVersion->id,
                'organisation_id' => $author instanceof Organisation ? $author->id : null,
                'user_id' => auth()->user()->id
            ])->first();
        }
        else {
            return $this->runningSimulations()->where([
                'organisation_id' => $this instanceof Organisation ? $this->id : null,
                'user_id' => auth()->user()->id
            ])->get();
        }
    }

    /**
     * Gibt die öffentlichen Prozesse des Benutzers zurück.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function publicProcesses(): Collection {
        return $this->processes()
            ->where('visibility', '=', Visibility::Public->value)
            ->get()
            ->filter(fn(Process $process) => $process->hasPublicLicense());
    }

    /**
     * Gibt die öffentlichen Lösungen des Benutzers zurück.
     * @return \Illuminate\Database\Eloquent\Collection<Solution>
     * @noinspection PhpUnused
     */
    public function publicSolutions(): Collection {
        return $this->solutions()
            ->where('visibility', '=', Visibility::Public->value)
            ->get()
            ->filter(fn(Solution $solution) => $solution->hasPublicLicense());
    }

    /**
     * Prozesse, die mindestens eine publizierte Version haben.
     * Der aktuellste, publizierte Graph wird mitgeladen.
     * @return Collection<Process>
     */
    public function publishedProcesses() {
        $processes = $this->processes()
            ->with(['latestPublishedVersion', 'author'])
            ->where('latest_published_version_id', '!=', null)
            ->get();

        $processes->each(fn(Process $process) => $process->latestPublishedVersion->setRelation('process', $process));

        return $processes;
    }

    /**
     * All last completed (published) process versions of the user.
     * @return Collection<ProcessVersion>
     */
    public function latestPublishedProcessVersions(): Collection {
        return $this->publishedProcesses()->pluck('latestPublishedVersion');
    }

    /**
     * @return Collection
     */
    public function publishedProcessVersions() {
        $processes = $this->processes()->with(['author'])->withWhereHas('versions', function ($query) {
            $query->where('published_at', '!=', null)->latest('published_at')->take(10);
        })->get();

        return $processes->pluck('versions')->flatten();
    }

    /**
     * Get accessible published and licensed processes versions from the cache.
     * @return Collection
     */
    public function accessiblePublishedProcessVersions() {
        $cache = $this->cache();
        $publishedProcessVersionIds = $cache['published_process_version_ids'] ?? [];
        $licensesProcessVersionIds = $cache['licenses_process_version_ids'] ?? [];

        return ProcessVersion::with(['process', 'environments'])->findMany(collect([
            ...$publishedProcessVersionIds,
            ...$licensesProcessVersionIds
        ])->unique());
    }
}
