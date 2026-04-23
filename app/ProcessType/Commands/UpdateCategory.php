<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Category;
use App\ProcessType\Definition;

/**
 * Class UpdateCategory
 * @package App\ProcessType\Commands
 */
class UpdateCategory extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['categories'];

    /**
     * Menu-Item aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $categoryId = $this->payload['id'];

        $this->definition->categories = $this->definition->categories->map(function (Category $category) use ($categoryId) {
            if ($category->id === $categoryId) {
                return Category::make($this->payload);
            }

            return $category;
        });

        return $this->definition;
    }

}
