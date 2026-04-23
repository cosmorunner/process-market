<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Output;

/**
 * Class StoreActionTypeOutputBulk
 * @package App\ProcessType\Commands
 */
class StoreActionTypeOutputBulk extends Command {

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
            $parts = array_map('trim', explode(';', $row));
            $name = str_ends_with($parts[0], '!') ? substr($parts[0], 0, -1) : $parts[0];

            if (isset($parts[1]) && empty($parts[1])) {
                $default = '_empty_';
            }
            else if (isset($parts[1]) && $parts[1] === 'null') {
                $default = null;
            }
            else {
                $default = $parts[1] ?? '';
            }

            $payload = [
                'action_type_id' => $this->payload['action_type_id'],
                'name' => $name,
                'validation' => str_ends_with($parts[0], '!') ? ['required'] : [],
                'default' => $default,
                'include_in_process_data' => isset($parts[2]) && $parts[2] == '1',
                'include_in_input_data' => isset($parts[3]) && $parts[3] == '1',
                'load_process_data_field' => isset($parts[4]) && $parts[4] == '1',
                'create_form_field' => isset($parts[5]) && $parts[5] == '1'
            ];

            $payload = $this->getType($payload);

            $this->definition = (new StoreActionTypeOutput($payload, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }

    /**
     * @param $payload
     * @return array
     */
    private function getType($payload) {
        if (str_starts_with($payload['name'], '=')) {
            $payload['type'] = Output::TYPE_ARRAY;
            $payload['name'] = substr($payload['name'], 1);
            $payload['default'] = [];

            return $payload;
        }
        if (str_starts_with($payload['name'], '~')) {
            $payload['type'] = Output::TYPE_OBJECT;
            $payload['name'] = substr($payload['name'], 1);
            $payload['default'] = [];

            return $payload;
        }

        $payload['type'] = Output::TYPE_BASIC;

        return $payload;
    }
}