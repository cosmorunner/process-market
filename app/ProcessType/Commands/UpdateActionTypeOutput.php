<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Output;

/**
 * Class UpdateActionTypeOutput
 * @package App\ProcessType\Commands
 */
class UpdateActionTypeOutput extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = false;

    /**
     * Aktionstyp-Output aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $actionType = $this->definition->actionType($this->payload['action_type_id']);

        if ($actionType instanceof ActionType) {
            $outputs = $actionType->outputs->map(function (Output $output) {

                if ($output->name === $this->payload['old_name']) {
                    return Output::make($this->payload);
                }

                return $output;
            });

            $actionType->outputs = $outputs;
        }

        return $this->definition;
    }

}
