<?php

namespace App\Environment\Commands;

use App\Environment\Group;
use App\Models\Environment;

/**
 * Class StoreGroup
 * @package App\Environment\Commands
 */
class StoreGroup extends Command {

    private Group $group;

    /**
     * Erstellen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $this->group = Group::make($this->payload);

        $groups = $this->environment->blueprint->groups;
        $groups->add($this->group);

        $this->environment->updateBlueprint('groups', $groups);

        return $this->environment;
    }

    /**
     * Nach dem Anlegen der Gruppe die Direktor-Gruppen-Rolle erzeugen.
     * @param Environment $environment
     * @return array
     */
    public function afterExecutingCommands(Environment $environment): array {
        return [new StoreGroupRole([
            'name' => 'Direktor',
            'group_id' => $this->group->id,
        ], $environment)];
    }
}
