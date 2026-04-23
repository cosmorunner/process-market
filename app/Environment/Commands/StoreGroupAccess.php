<?php

namespace App\Environment\Commands;

use App\Environment\GroupAccess;
use App\Models\Environment;

/**
 * Class StoreGroupAccess
 * @package App\Environment\Commands
 */
class StoreGroupAccess extends Command {

    /**
     * Erstellt eine Gruppen-Rolle
     * @return Environment
     */
    public function command(): Environment {
        $groupAccesses = $this->environment->blueprint->groupAccesses;
        $groupAccesses->add(GroupAccess::make([
            'group_id' => $this->payload['group_id'] ?? null,
            'user_id' => $this->payload['user_id'],
            'role_id' => $this->payload['role_id']
        ]));

        $this->environment->updateBlueprint('groupAccesses', $groupAccesses);

        return $this->environment;
    }
}
