<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Category;
use App\ProcessType\Definition;

/**
 * Class StoreCategory
 * @package App\ProcessType\Commands
 */
class StoreCategory extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['categories'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $sort = $this->definition->categories->max('sort') + 1;
        $this->payload['sort'] = $sort;

        $category = Category::make($this->payload);
        $this->definition->categories->add($category);

        return $this->definition;
    }

}
