<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;

/**
 * Class StoreActionRule
 * @package App\ProcessType\Commands
 */
class StoreActionRule extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $model = ActionRule::make($this->payload);
        $actionType = $this->definition->actionType($model->action_type_id);

        if ($actionType instanceof ActionType) {
            $actionType->actionRules->add($model);
            $this->statusTypeContext = $model->status_type_id;
            $this->positionModelId = $actionType->id;
        }

        return $this->definition;
    }

}
