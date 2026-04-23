<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Listener;

/**
 * Class UpdateListener
 * @package App\ProcessType\Commands
 */
class UpdateListener extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['listeners'];

    /**
     * Verknüpfungstyp aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->listeners = $this->definition->listeners->map(function (Listener $listener) {
            if ($this->payload['id'] === $listener->id) {
                return Listener::make($this->payload);
            }

            return $listener;
        });

        return $this->definition;
    }

}
