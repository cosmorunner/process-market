<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateStatusTypes
 * @package App\ProcessType\Commands
 */
class UpdateMenuItems extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['menu_items'];

    /**
     * Ausführung des Commands.
     * @return Definition
     */
    protected function command(): Definition {
        $menuItems = $this->payload['items'];

        foreach ($menuItems as $menuItem) {
            $this->definition = (new UpdateMenuItem($menuItem, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }

}
