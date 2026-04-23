<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Processor;

/**
 * Class UpdateProcessor
 * @package App\ProcessType\Commands
 */
class UpdateProcessor extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    /**
     * Prozessor aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $processorId = $this->payload['id'];

        $actionType = $this->definition->actionType($this->payload['action_type_id']);
        $actionType->processors = $actionType->processors->map(function (Processor $processor) use ($processorId) {
            if ($processor->id === $processorId) {

                // "single_space" Platzhalter in String durch Leerzeichen ersetzen weil Laravel sonst automatisch Strings
                // trimmed oder zu null macht
                foreach ($this->payload['options'] as $key => $value) {
                    if (is_string($value)) {
                        $this->payload['options'][$key] = str_replace('single_space', ' ', $value);
                    }
                }

                return Processor::make($this->payload);
            }

            return $processor;
        });

        return $this->definition;
    }

}
