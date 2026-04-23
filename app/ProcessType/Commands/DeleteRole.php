<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Role;
use App\Traits\DeletesProcessorModelUsage;

/**
 * Class DeleteRole
 * @package App\ProcessType\Commands
 */
class DeleteRole extends Command {

    use DeletesProcessorModelUsage;

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['public_role_id', 'default_role_id', 'roles'];

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $roleId = $this->payload['id'];
        $toDeleteRole = $this->definition->role($this->payload['id']);

        // Die letzte Rolle darf nicht gelöscht werden.
        if ($this->definition->roles->count() <= 1) {
            return $this->definition;
        }

        $this->definition->roles = $this->definition->roles->filter(fn(Role $role) => $role->id !== $toDeleteRole->id)->values();

        if ($this->definition->default_role_id === $roleId) {
            $this->definition->default_role_id = null;
        }

        if ($this->definition->public_role_id === $roleId) {
            $this->definition->public_role_id = null;
        }

        // Falls es nach dem Löschen nur noch eine Rolle gibt und es vorher eine Standard-Rolle gab,
        // soll diese Rolle die neue Standard-Rolle werden.
        if ($this->definition->roles->count() === 1 && $this->definition->defaultRole) {
            $this->definition->default_role_id = $this->definition->roles->first()->id;
        }

        return $this->definition;
    }

    /**
     * Nach dem Löschen einer Rolle muss die Nutzung der Rolle allen Prozessoren aller Aktionstypen entfernt werden.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $roleId = $this->payload['id'];

        return $this->deleteProcessorModelUsages($updatedDefinition, 'role', $roleId);
    }

}
