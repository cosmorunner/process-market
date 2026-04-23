<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Listener;

/**
 * Class StoreListener
 * @package App\ProcessType\Commands
 */
class StoreListener extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['listeners'];

    /**
     * Verknüpfungstyp anlegen.
     * @return Definition
     */
    public function command(): Definition {
        $listener = Listener::make($this->payload);
        $this->definition->listeners->add($listener);

        return $this->definition;
    }

}
