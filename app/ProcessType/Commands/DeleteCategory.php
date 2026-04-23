<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Category;
use App\ProcessType\Definition;
use App\Traits\DeletesProcessorModelUsage;

/**
 * Class DeleteCategory
 * @package App\ProcessType\Commands
 */
class DeleteCategory extends Command {

    use DeletesProcessorModelUsage;

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
        $definition = $this->definition;
        $definition->categories = $this->definition->categories
            ->filter(fn(Category $category) => $category->id !== $this->payload['id'])->values();

        return $definition;
    }


    /**
     * Nutzung der Kategorie bei den Aktionstypen entfernen.
     * Kategorien werden der Standard-System-Kategorie (locked = true) zugeordnet.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $commands = [];
        $categoryId = $this->payload['id'];
        $defaultCategory = $updatedDefinition->categories->firstWhere('locked', '=', true);

        foreach ($updatedDefinition->actionTypes as $actionType) {
            // Falls bei einem Aktionstyp die Kategorie gelöscht wurde, erhält der Aktionstyp
            // die Standard "Workflow" Kategorie
            if ($actionType->category_id === $categoryId && $defaultCategory instanceof Category) {
                $actionType->category_id = $defaultCategory->id;
                $commands[] = new UpdateActionType($actionType->toArray(), $updatedDefinition, $this->processVersion);
            }
        }

        return $commands;
    }

}
