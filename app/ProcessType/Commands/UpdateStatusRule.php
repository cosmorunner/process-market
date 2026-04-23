<?php


namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\StatusRule;

/**
 * Class UpdateStatusRule
 * @package App\ProcessType\Commands
 */
class UpdateStatusRule extends Command {

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

        $actionType->statusRules = $actionType->statusRules->map(function (StatusRule $statusRule) {
            if ($statusRule->id === $this->payload['id']) {
                return StatusRule::make($this->payload);
            }

            return $statusRule;
        });

        return $this->definition;
    }

}
