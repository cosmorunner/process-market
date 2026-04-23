<?php

namespace App\Environment\Commands;

use App\Environment\SystemAccess;
use App\Models\Environment;

/**
 * Löschen eines System-Zugriffs
 * Class DeleteSystemAccess
 * @package App\Environment\Commands
 */
class DeleteSystemAccess extends Command {

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $systemAccesses = $this->environment->blueprint->systemAccesses->filter(fn(SystemAccess $systemAccess) => $systemAccess->user_id !== $this->payload['user_id'] ?? null);
        $this->environment->updateBlueprint('systemAccesses', $systemAccesses);

        return $this->environment;
    }
}
