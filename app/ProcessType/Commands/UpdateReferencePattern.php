<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateReferencePattern
 * @package App\ProcessType\Commands
 */
class UpdateReferencePattern extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['reference_pattern'];

    /**
     * Flag indicating whether the graph must be recalculated after the command. For example, for StoreActionType or DeleteActionRule
     * the graph must be recalculated.
     * @var bool
     */
    public $recalculate = false;

    /**
     * Rolle aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->reference_pattern = (string) ($this->payload['reference_pattern'] ?? '');

        return $this->definition;
    }

}
