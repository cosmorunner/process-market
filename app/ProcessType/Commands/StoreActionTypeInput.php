<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Input;

/**
 * Class StoreActionTypeInput
 * @package App\ProcessType\Commands
 */
class StoreActionTypeInput extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = false;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $model = Input::make($this->payload);
        $actionType = $this->definition->actionType($this->payload['action_type_id']);

        if ($actionType instanceof ActionType) {
            $actionType->inputs->add($model);
        }

        return $this->definition;
    }
}
