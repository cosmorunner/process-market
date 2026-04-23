<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateCategories
 * @package App\ProcessType\Commands
 */
class UpdateCategories extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['categories'];

    /**
     * Statustypen aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $categories = $this->payload['items'];

        foreach ($categories as $category) {
            $this->definition = (new UpdateCategory($category, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }

}
