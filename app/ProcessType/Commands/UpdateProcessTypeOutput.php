<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Output;

/**
 * Class UpdateProcessTypeOutput
 * @package App\ProcessType\Commands
 */
class UpdateProcessTypeOutput extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['outputs'];

    public $recalculate = false;

    /**
     * Prozesstyp-Output aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $outputs = $this->definition->outputs->map(function (Output $output) {
            if ($output->name === ($this->payload['old_name'])) {
                return Output::make($this->payload);
            }

            return $output;
        });

        $this->definition->outputs = $outputs;

        return $this->definition;
    }

}
