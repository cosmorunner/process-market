<?php


namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\StatusRule;

/**
 * Class DeleteStatusRule
 * @package App\ProcessType\Commands
 */
class DeleteStatusRule extends Command {

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
        $model = $this->definition->actionType($this->payload['action_type_id']);

        $model->statusRules = $model->statusRules->filter(fn(StatusRule $rule) => $rule->status_type_id !== $this->payload['status_type_id'])->values();

        return $this->definition;
    }

}
