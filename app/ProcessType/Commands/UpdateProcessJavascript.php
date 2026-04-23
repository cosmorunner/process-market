<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateProcessJavascript
 * @package App\ProcessType\Commands
 */
class UpdateProcessJavascript extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['javascript'];

    /**
     * Javascript aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->javascript = json_decode($this->payload['javascript'], true);

        return $this->definition;
    }

}