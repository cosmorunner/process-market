<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateActionTypes
 * @package App\ProcessType\Commands
 */
class UpdateActionTypes extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Statustypen aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $actionTypes = $this->payload['items'];

        foreach ($actionTypes as $actionType) {
            $this->definition = (new UpdateActionType($actionType, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }

}
