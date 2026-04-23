<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\MenuItem;

/**
 * Class UpdateMenuItem
 * @package App\ProcessType\Commands
 */
class UpdateMenuItem extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['menu_items'];

    /**
     * Menu-Item aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $menuItemId = $this->payload['id'];

        $this->definition->menuItems = $this->definition->menuItems->map(function (MenuItem $menuItem) use ($menuItemId) {
            if ($menuItem->id === $menuItemId) {
                return MenuItem::make($this->payload);
            }

            return $menuItem;
        });

        return $this->definition;
    }

}
