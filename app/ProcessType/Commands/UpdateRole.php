<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Role;

/**
 * Class UpdateRole
 * @package App\ProcessType\Commands
 */
class UpdateRole extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['roles'];

    /**
     * Flag indicating whether the graph must be recalculated after the command. For example, for StoreActionType or DeleteActionRule
     * the graph must be recalculated.
     * @var bool
     */
    public $recalculate = true;

    /**
     * Rolle aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->roles = $this->definition->roles->map(function (Role $role) {
            if ($this->payload['id'] === $role->id) {
                return Role::make($this->payload);
            }

            return $role;
        });

        return $this->definition;
    }

}
