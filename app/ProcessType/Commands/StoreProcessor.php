<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Processor;

/**
 * Class StoreProcessor
 * @package App\ProcessType\Commands
 */
class StoreProcessor extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    /**
     * Prozessor erstellen.
     * @return Definition
     */
    public function command(): Definition {
        $actionType = $this->definition->actionType($this->payload['action_type_id']);

        if ($actionType->usesManualProcessorSorting()) {
            $this->payload['sort'] = $actionType->processors->count();
        }
        else {
            $this->payload['sort'] = null;
        }

        // "single_space" Platzhalter in String durch Leerzeichen ersetzen weil Laravel sonst automatisch Strings
        // trimmed oder zu null macht
        foreach ($this->payload['options'] as $key => $value) {
            if (is_string($value)) {
                $this->payload['options'][$key] = str_replace('single_space', ' ', $value);
            }
        }

        $actionType->processors = $actionType->processors->add(Processor::make($this->payload));

        return $this->definition;
    }

}
