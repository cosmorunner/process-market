<?php

namespace App\Environment\Commands;

use App\Environment\GroupAccess;
use App\Models\Environment;

/**
 * Delete the group access of a user
 * Class DeleteGroupAccess
 * @package App\Environment\Commands
 */
class DeleteGroupAccess extends Command {

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $groupAccessses = $this->environment->blueprint->groupAccesses->filter(fn(GroupAccess $groupAccess) => $groupAccess->user_id !== $this->payload['user_id'] ?? null);
        $this->environment->updateBlueprint('groupAccesses', $groupAccessses);

        return $this->environment;
    }
}
