<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\MenuItem;

/**
 * Class StoreMenuItem
 * @package App\ProcessType\Commands
 */
class StoreMenuItem extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['menu_items'];

    /**
     * Menu-Item hinzufügen.
     * @return Definition
     */
    public function command(): Definition {
        $sort = $this->definition->menuItems->max('sort') + 1;
        $this->payload['sort'] = $sort;

        $menuItem = MenuItem::make($this->payload);
        $this->definition->menuItems = $this->definition->menuItems->add($menuItem);

        return $this->definition;
    }

}
