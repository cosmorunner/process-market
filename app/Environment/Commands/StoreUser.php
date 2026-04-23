<?php

namespace App\Environment\Commands;

use App\Environment\User;
use App\Models\Environment;
use Ramsey\Uuid\Uuid;

/**
 * Class StoreUser
 * @package App\Environment\Commands
 */
class StoreUser extends Command {

    private User $user;

    /**
     * Erstellen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $this->user = User::make($this->payload);

        $users = $this->environment->blueprint->users;
        $users->add($this->user);

        $this->environment->updateBlueprint('users', $users);

        return $this->environment;
    }

    /**
     * Nach dem Anlegen des Benutzers den Benutzer zur Gruppe hinzufügen und Debug-Modus aktivieren.
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

        // Systemzugriff
        $systemAccess = new StoreSystemAccess([
            'user_id' => $this->user->id,
            'role_id' => config('allisa.simulation.default_system_role_id')
        ], $environment);

        // Debugmode
        $setting = new StoreSetting([
            'name' => 'debug.mode',
            'value' => true,
            'owner_id' => $this->user->id,
            'owner_type' => 'App\\Models\\User'
        ], $environment);

        return [$groupAccess, $systemAccess, $setting];
    }
}
