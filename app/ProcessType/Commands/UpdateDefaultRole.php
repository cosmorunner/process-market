<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateDefaultRole
 * @package App\ProcessType\Commands
 */
class UpdateDefaultRole extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['default_role_id'];

    /**
     * Rolle aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->default_role_id = $this->payload['default_role_id'] ?? null;

        return $this->definition;
    }

}
