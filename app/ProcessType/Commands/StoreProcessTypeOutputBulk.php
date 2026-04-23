<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Output;

/**
 * Class StoreProcessTypeOutput
 * @package App\ProcessType\Commands
 */
class StoreProcessTypeOutputBulk extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['outputs'];

    public $recalculate = false;

    /**
     * Erstellen eines Prozesstyp-Outputs.
     * @return Definition
     */
    public function command(): Definition {
        foreach ($this->payload['value'] as $row) {
            $values = array_map('trim', explode(';', $row));

            if (isset($values[1]) && empty($values[1])) {
                $default = '_empty_';
            }
            else if (isset($values[1]) && $values[1] === 'null') {
                $default = null;
            }
            else {
                $default = $values[1] ?? '';
            }

            $payload = [
                'create_form_field' => false,
                'default' => $default,
                'include_in_process_data' => false,
                'name' => $values[0],
                'validation' => ["nullable"]
            ];

            $payload = $this->setType($payload);

            $this->definition = (new StoreProcessTypeOutput($payload, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }

    /**
     * @param $payload
     * @return array
     */
    private function setType($payload) {
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
