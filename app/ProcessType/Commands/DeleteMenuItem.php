<?php

namespace App\ProcessType\Commands;

use App\Enums\ProcessRolePermissions;
use App\ProcessType\Definition;
use App\ProcessType\MenuItem;
use App\ProcessType\Permission;

/**
 * Class DeleteMenuItem
 * @package App\ProcessType\Commands
 */
class DeleteMenuItem extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['menu_items'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->menuItems = $this->definition->menuItems
            ->filter(fn(MenuItem $menuItem) => $menuItem->id !== $this->payload['id'])->values();

        return $this->definition;
    }

    /**
     * Zusätzlich das Recht zu dem Menu-Einträg von allen Rollen entfernen.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        return [new DeletePermissionFromAllRoles([
            'ident' => ident(ProcessRolePermissions::ViewMenuItem->value, $this->payload['id'])
        ], $updatedDefinition, $this->processVersion)];
    }
}
