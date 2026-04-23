<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Permission;
use App\ProcessType\Role;

/**
 * Entfernt ein Recht anhand einer "ident" von allen Rollen des Prozesstyps.
 * Class DeletePermissionFromAllRoles
 * @package App\ProcessType\Commands
 */
class DeletePermissionFromAllRoles extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['roles'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * $this->payload['ident'] --> Rechte-Ident welches von allen Rollen entfernt werden soll.
     * @return Definition
     */
    public function command(): Definition {
        $ident = $this->payload['ident'] ?? '';

        $this->definition->roles = $this->definition->roles->map(function (Role $role) use ($ident) {
            $role->permissions = $role->permissions->filter(fn(Permission $permission) => $permission->ident !== $ident);

            return $role;
        })->values();

        return $this->definition;
    }

}
