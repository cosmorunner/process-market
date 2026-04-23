<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Legt fest, dass die Entität synchronisierbar mit einer Allisa Plattform ist. D.h. die Entität kann
 * zu einer Allisa Plattform exportiert werden.
 */
interface Syncable {

    /**
     * Synchronisiert die Entität zu einer Collection von Allisa Plattformen.
     * @return Collection Sychronisationen.
     */
    public function syncTo(Collection $systems): Collection;

    /**
     * Synchronisationen der Entität
     * @return MorphMany
     */
    public function synchronizations(): MorphMany;

    /**
     * Alle Synchronisationen der Entität zu einer Liste von Systemen.
     * @param Collection $sytems
     * @return Collection
     */
    public function synchronizationsOfSystems(Collection $sytems): Collection;
}
