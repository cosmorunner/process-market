<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Input;

/**
 * Class DeleteActionTypeOutput
 * @package App\ProcessType\Commands
 */
class DeleteActionTypeInput extends Command {

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
     * @return mixed
     */
    public function command(): Definition {
        $model = $this->definition->actionType($this->payload['action_type_id']);

        $inputs = $model->inputs->filter(fn(Input $input) => $input->name !== $this->payload['name'])->values();
        $model->inputs = $inputs;

        return $this->definition;
    }

}
