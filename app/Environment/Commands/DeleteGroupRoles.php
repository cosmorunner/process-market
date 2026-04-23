<?php

namespace App\Environment\Commands;

use App\Environment\GroupRole;
use App\Models\Environment;

/**
 * Löschen einer Gruppen-Rolle
 * Class DeleteGroup
 * @package App\Environment\Commands
 */
class DeleteGroupRoles extends Command {

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $groupRoles = $this->environment->blueprint->groupRoles->filter(fn(GroupRole $groupRole) => $groupRole->group_id !== $this->payload['group_id'] ?? null);
        $this->environment->updateBlueprint('groupRoles', $groupRoles);

        return $this->environment;
    }
}
