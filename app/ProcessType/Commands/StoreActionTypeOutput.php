<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Input;
use App\ProcessType\Output;

/**
 * Class StoreActionTypeOutput
 * @package App\ProcessType\Commands
 */
class StoreActionTypeOutput extends Command {

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
        $model = Output::make($this->payload);
        $actionType = $this->definition->actionType($this->payload['action_type_id']);

        if ($actionType instanceof ActionType) {
            $actionType->outputs->add($model);

            // Prozesdatensatz hinzufügen falls noch nicht existiert.
            if (($this->payload['create_form_field'] ?? false) && !$actionType->formFieldExists($model->name)) {
                $actionType->createFormfieldFromValidationRules($model->name, $this->payload['validation'] ?? []);
            }
        }

        return $this->definition;
    }

    /**
     * Optional zusätzlich als Prozesstyp-Output hinzufügen.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $includeInProcessData = $this->payload['include_in_process_data'] ?? false;
        $exists = $updatedDefinition->outputs->pluck('name')->contains($this->payload['name']);
        $commands = [];

        if ($includeInProcessData && !$exists) {
            $payload = array_merge($this->payload, ['validation' => []]);

            $commands[] = new StoreProcessTypeOutput($payload, $updatedDefinition, $this->processVersion);
        }

        $includeInInputData = $this->payload['include_in_input_data'] ?? false;
        $actionType = $updatedDefinition->actionType($this->payload['action_type_id']);
        $exists = $actionType->inputs->pluck('name')->contains($this->payload['name']);

        if ($includeInInputData && !$exists) {
            $loadProcessDataField = $this->payload['load_process_data_field'] ?? false;

            $payload = [
                'action_type_id' => $actionType->id,
                'name' => $this->payload['name'],
                'type' => Input::TYPE_AUTO
            ];

            if ($loadProcessDataField) {
                $payload['value'] = "[[process.outputs." . $payload['name'] . "((Prozess-Daten - " . $payload['name'] . "))]]";
            }

            $commands[] = new StoreActionTypeInput($payload, $updatedDefinition, $this->processVersion);
        }

        return $commands;
    }
}