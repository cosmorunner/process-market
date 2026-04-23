<?php

namespace App\Environment\Commands;

use App\Environment\Group;
use App\Environment\GroupAccess;
use App\Models\Environment;

/**
 * Class DeleteGroup
 * @package App\Environment\Commands
 */
class DeleteGroup extends Command {

    /**
     * Vor dem Löschen der Gruppe alle Gruppen-Rollen Löschen.
     * @param Environment $environment
     * @return array
     */
    public function beforeExecutingCommands(Environment $environment): array {
        $deleteGroupRoles = new DeleteGroupRoles([
            'group_id' => $this->payload['id'],
        ], $environment);

        $commands = [$deleteGroupRoles];

        foreach ($environment->blueprint->groupAccesses as $groupAccess) {
            /* @var GroupAccess $groupAccess */
            if ($groupAccess->group_id === $this->payload['id']) {
                $user = $environment->blueprint->users->firstWhere('id', '=', $groupAccess->user_id);

                if ($user) {
                    $commands[] = new DeleteUser(['id' => $user->id], $environment);
                }
            }
        }

        $commands[] = new DeleteProcessAccess(['accessible_model_id' => $this->payload['id']], $environment);

        return $commands;
    }

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $groups = $this->environment->blueprint->groups->filter(fn(Group $group) => $group->id !== $this->payload['id'] ?? null);
        $this->environment->updateBlueprint('groups', $groups);

        return $this->environment;
    }
}
