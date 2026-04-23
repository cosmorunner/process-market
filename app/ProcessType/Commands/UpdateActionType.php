<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;

/**
 * Class UpdateActionTypeOutput
 * @package App\ProcessType\Commands
 */
class UpdateActionType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Aktionstyp aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $actionTypeId = $this->payload['id'];

        $this->definition->actionTypes = $this->definition->actionTypes->map(function (ActionType $actionType) use ($actionTypeId) {
            if ($actionType->id === $actionTypeId) {
                return ActionType::make($this->payload);
            }

            return $actionType;
        });

        return $this->definition;
    }

}
