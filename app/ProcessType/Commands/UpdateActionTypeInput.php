<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Input;

/**
 * Class UpdateActionTypeInput
 * @package App\ProcessType\Commands
 */
class UpdateActionTypeInput extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    /**
     * Aktionstyp-Output aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $actionType = $this->definition->actionType($this->payload['action_type_id']);

        if ($actionType instanceof ActionType) {
            $inputs = $actionType->inputs->map(function (Input $input) {
                if ($input->name === ($this->payload['old_name'] ?? '')) {
                    return Input::make($this->payload);
                }

                return $input;
            });

            $actionType->inputs = $inputs;
        }

        return $this->definition;
    }

}
