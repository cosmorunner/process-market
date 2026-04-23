<?php

namespace App\Interfaces;

use App\Models\Demo;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\SolutionVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Gibt an, dass die Entität mit Prozessen, Lösungen, Lizenzen, Graphen, SolutionVerions umgeht, d.h.
 * besitzen kann, Simulationen ausführen kann, Demos starten kann.
 */
interface HandlesProcesses {

    /**
     * Prozesse der Entität.
     * @return HasMany
     */
    public function processes();

    /**
     * Lösungen der Entität
     * @return HasMany
     */
    public function solutions();

    /**
     * Simulationen der Entität.
     * @return HasMany
     */
    public function simulations();

    /**
     * Demos der Entität.
     * @return HasMany
     */
    public function demos();

    /**
     * Laufende Simulationen der Entität.
     * @return Collection
     */
    public function runningSimulations();

    /**
     * Laufende Demos der Entität.
     * @return Collection
     */
    public function runningDemos();

    /**
     * Laufende Simulationen der Entität von dem angemeldeten Benutzer.
     * @param ProcessVersion|null $processVersion Optionale Filterung nach eine Simulation von einem Graph.
     * @return Collection|Simulation|Model|null
     */
    public function runningUserSimulations(ProcessVersion $processVersion = null): Collection|Simulation|Model|null;

    /**
     * Laufende Demos der Entität von dem angemeldeten Benutzer.
     * @param SolutionVersion|null $solutionVersion Optionale Filterung nach einer bestimmten Lösungsversion.
     * @return Collection|Demo|Model|null
     */
    public function runningUserDemos(SolutionVersion $solutionVersion = null): Collection|Demo|Model|null;

    /**
     * Gibt die öffentlichen Prozesse der Entität zurück.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function publicProcesses(): Collection;

    /**
     * Gibt die öffentlichen Lösungen der Entität zurück.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function publicSolutions(): Collection;

    /**
     * Pfad zu den Prozessen der Entität.
     * @return string
     */
    public function profileProcessesPath(): string;

    /**
     * Pfad zu den Lösungen der Entität.
     * @return string
     */
    public function profileSolutionsPath(): string;

    /**
     * Pfad zu den Lizenzen der Entität.
     * @return string
     */
    public function profileLicensesPath(): string;
}
