<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdatePublicRole
 * @package App\ProcessType\Commands
 */
class UpdatePublicRole extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['public_role_id'];

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
        $this->definition->public_role_id = $this->payload['public_role_id'] ?? null;

        return $this->definition;
    }

}
