<?php

namespace App\Environment\Commands;

use App\Environment\User;
use App\Models\Environment;
use Ramsey\Uuid\Uuid;

/**
 * Class UpdateUser
 * @package App\Environment\Commands
 */
class UpdateUser extends Command {

    private User $user;

    /**
     * Erstellen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $updatedUser = User::make($this->payload);
        $users = $this->environment->blueprint->users;
        $this->user = $updatedUser;
        $users = $users->map(fn(User $user) => $user->id === $updatedUser->id ? $updatedUser : $user);
        $this->environment->updateBlueprint('users', $users);

        return $this->environment;
    }

    /**
     * After updating the user, remove the user from the previous group and add him to the new group.
     * @param Environment $environment
     * @return array
     */
    public function afterExecutingCommands(Environment $environment): array {
        // Wenn keine Gruppen-Id mitgesandt wird, wird der Benutzer der "Standard"-Gruppe zugewiesen.
        $groupId = $this->payload['group_id'] ?? '';
        $groupId = Uuid::isValid($groupId) ? $groupId : config('allisa.simulation.default_group_id');
        $directorRoleId = $environment->blueprint->groupRoles->firstWhere('group_id', '=', $groupId)->id ?? config('allisa.simulation.default_director_role_id');

        // Gruppenzugriff
        $groupAccess = new StoreGroupAccess([
            'group_id' => $groupId,
            'user_id' => $this->user->id,
            'role_id' => $directorRoleId
        ], $environment);

        $deleteGroupAccess = new DeleteGroupAccess(['user_id' => $this->user->id ?? ''], $environment);

        return [$deleteGroupAccess, $groupAccess];
    }
}
