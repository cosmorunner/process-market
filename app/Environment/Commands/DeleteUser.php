<?php

namespace App\Environment\Commands;

use App\Environment\User;
use App\Models\Environment;

/**
 * Class DeleteUser
 * @package App\Environment\Commands
 */
class DeleteUser extends Command {

    /**
     * Vor dem Löschen des Benutzers den Gruppen-Zugriff löschen.
     * @param Environment $environment
     * @return array
     */
    public function beforeExecutingCommands(Environment $environment): array {
        /* @var User $user */
        $user = $this->environment->blueprint->users->firstWhere('id', '=', $this->payload['id']);

        $deleteGroupAccess = new DeleteGroupAccess(['user_id' => $user->id ?? ''], $environment);
        $deleteSystemAccess = new DeleteSystemAccess(['user_id' => $user->id ?? ''], $environment);
        $deleteSystemSettings = new DeleteSetting(['owner_id' => $user->id ?? ''], $environment);
        $deleteProcessAccess = new DeleteProcessAccess(['accessible_model_id' => $user->id ?? ''], $environment);

        return [$deleteGroupAccess, $deleteSystemAccess, $deleteSystemSettings, $deleteProcessAccess];
    }

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $users = $this->environment->blueprint->users->filter(fn(User $user) => $user->id !== $this->payload['id'] ?? null);
        $this->environment->updateBlueprint('users', $users);

        return $this->environment;
    }

}
