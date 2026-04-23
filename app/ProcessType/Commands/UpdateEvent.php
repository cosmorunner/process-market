<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Event;

/**
 * Class UpdateEvent
 * @package App\ProcessType\Commands
 */
class UpdateEvent extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['events'];

    /**
     * Verknüpfungstyp aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->events = $this->definition->events->map(function (Event $event) {
            if ($this->payload['id'] === $event->id) {
                return Event::make($this->payload);
            }

            return $event;
        });

        return $this->definition;
    }

}
