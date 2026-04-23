<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Listener;

/**
 * Class DeleteListener
 * @package App\ProcessType\Commands
 */
class DeleteListener extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['listeners'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $definition = $this->definition;
        $definition->listeners = $this->definition->listeners->filter(fn(Listener $listener) => $listener->id !== $this->payload['id'])->values();

        return $definition;
    }

}
