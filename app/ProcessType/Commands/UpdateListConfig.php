<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\ListConfig;

/**
 * Class UpdateListConfig
 * @package App\ProcessType\Commands
 */
class UpdateListConfig extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['list_configs'];

    /**
     * Listenkonfiguration aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $updatedListConfig = new ListConfig($this->payload);
        $this->definition->listConfigs = $this->definition->listConfigs->map(function (ListConfig $listConfig) use ($updatedListConfig) {
            if($listConfig->id == $updatedListConfig->id) {
                return $updatedListConfig;
            }

            return $listConfig;
        })->values();

        return $this->definition;
    }

}
