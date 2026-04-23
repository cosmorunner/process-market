<?php

namespace App\Environment\Commands;

use App\Environment\SystemAccess;
use App\Models\Environment;

/**
 * Class StoreSystemAccess
 * @package App\Environment\Commands
 */
class StoreSystemAccess extends Command {

    /**
     * Erstellt eine Gruppen-Rolle
     * @return Environment
     */
    public function command(): Environment {
        $systemAccesses = $this->environment->blueprint->systemAccesses;
        $systemAccesses->add(SystemAccess::make([
            'user_id' => $this->payload['user_id'],
            'role_id' => $this->payload['role_id']
        ]));

        $this->environment->updateBlueprint('systemAccesses', $systemAccesses);

        return $this->environment;
    }
}
