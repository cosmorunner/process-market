<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Event;

/**
 * Class StoreEvent
 * @package App\ProcessType\Commands
 */
class StoreEvent extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['events'];

    /**
     * Verknüpfungstyp anlegen.
     * @return Definition
     */
    public function command(): Definition {
        $event = Event::make($this->payload);
        $this->definition->events->add($event);

        return $this->definition;
    }

}
