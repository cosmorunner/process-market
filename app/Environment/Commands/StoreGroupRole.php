<?php

namespace App\Environment\Commands;

use App\Environment\GroupRole;
use App\Models\Environment;

/**
 * Class StoreRelation
 * @package App\Environment\Commands
 */
class StoreGroupRole extends Command {

    /**
     * Erstellt eine Gruppen-Rolle
     * @return Environment
     */
    public function command(): Environment {
        $groupRoles = $this->environment->blueprint->groupRoles;
        $groupRoles->add(GroupRole::make($this->payload));

        $this->environment->updateBlueprint('groupRoles', $groupRoles);

        return $this->environment;
    }
}
