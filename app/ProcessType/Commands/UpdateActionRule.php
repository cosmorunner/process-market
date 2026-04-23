<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;

/**
 * Class UpdateActionRule
 * @package App\ProcessType\Commands
 */
class UpdateActionRule extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Aktionsregel aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $actionType = $this->definition->actionType($this->payload['action_type_id']);

        if(!$actionType instanceof ActionType) {
            return $this->definition;
        }

        $actionType->actionRules = $actionType->actionRules->map(function (ActionRule $actionRule) {
            if ($actionRule->id === $this->payload['id']) {
                return ActionRule::make($this->payload);
            }

            return $actionRule;
        });

        return $this->definition;
    }

}
