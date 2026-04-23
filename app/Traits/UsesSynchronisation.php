<?php

namespace App\Traits;

use App\Models\License;
use App\Models\Synchronization;
use App\Models\System;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Implementiert die Synchronisation einer Entität zu einer Allisa Plattform.
 */
trait UsesSynchronisation {

    /**
     * Synchronisiert die Prozess-Version zu mehreren Systemen.
     * @param Collection $systems
     * @param License|null $license Falls die Synchronisation mittels einer Lizenz erfolgt.
     * @return Collection
     */
    public function syncTo(Collection $systems, License $license = null): Collection {
        return $systems->map(fn(System $system) => $system->sync($this, $system));
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
     * @noinspection PhpUnused
     */
    public function synchronizationsOfSystems(Collection $systems): Collection {
        return $this->synchronizations->whereIn('system_id', $systems->pluck('id'));
    }

}
