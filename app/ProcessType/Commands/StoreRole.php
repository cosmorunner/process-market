<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Role;

/**
 * Class StoreRole
 * @package App\ProcessType\Commands
 */
class StoreRole extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['default_role_id', 'roles'];

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $role = Role::make($this->payload);

        // Wenn es zuvor keine Rollen gab, wird die Rolle zur Standard-Rolle.
        if ($this->definition->roles->isEmpty()) {
            $this->definition->default_role_id = $role->id;
        }

        $this->definition->roles->add($role);

        return $this->definition;
    }

}
