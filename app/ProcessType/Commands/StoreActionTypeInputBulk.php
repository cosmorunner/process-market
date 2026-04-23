<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Input;

/**
 * Class StoreActionTypeInputBulk
 * @package App\ProcessType\Commands
 */
class StoreActionTypeInputBulk extends Command {

    /**
     * The graph must be recalculated.
     * @var bool
     */
    public $recalculate = false;

    /**
     * @return Definition
     */
    public function command(): Definition {
        foreach ($this->payload['value'] as $row) {
            $payload = ['action_type_id' => $this->payload['action_type_id']];
            $parts = explode(';', $row);

            // A value is provided. "#" to use the process data output of the provided field.
            if (count($parts) > 1) {
                if ($parts[1] === '#' && !str_starts_with($parts[0], '~') && !str_starts_with($parts[0], '=')) {
                    $payload['value'] = "[[process.outputs." . $parts[0] . "((Prozess-Daten - " . $parts[0] . "))]]";
                    $payload['type'] = Input::TYPE_AUTO;
                }
                else {
                    $payload['value'] = $parts[1];
                }
            }

            $payload['name'] = $parts[0];
            $payload = array_key_exists('type', $payload) ? $payload : $this->setType($payload);
            $model = Input::make($payload);
            $actionType = $this->definition->actionType($payload['action_type_id']);

            if ($actionType instanceof ActionType) {
                $actionType->inputs->add($model);
            }
        }

        return $this->definition;
    }

    /**
     * Sets the type based on the name and adjusts it if necessary.
     * @param $payload
     * @return array
     */
    private function setType($payload) {
        if (str_starts_with($payload['name'], '=')) {
            $payload['type'] = Input::TYPE_ARRAY;
            $payload['name'] = substr($payload['name'], 1);
            $payload['value'] = [];

            return $payload;
        }
        if (str_starts_with($payload['name'], '~')) {
            $payload['type'] = Input::TYPE_OBJECT;
            $payload['name'] = substr($payload['name'], 1);
            $payload['value'] = [];

            return $payload;
        }

        $payload['type'] = Input::TYPE_BASIC;

        return $payload;
    }
}