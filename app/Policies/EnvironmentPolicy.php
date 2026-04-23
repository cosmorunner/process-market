<?php

namespace App\Policies;

use App\Models\Environment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EnvironmentPolicy
 * @package App\Policies
 */
class EnvironmentPolicy {

    use HandlesAuthorization;

    /**
     * Ein Simulationsumgebung ändern dürfen.
     * @param User $user
     * @param Environment $environment
     * @return bool
     */
    public function update(User $user, Environment $environment) {
        $process = $environment->processVersion->process;

        if (is_null($process)) {
            return false;
        }

        // Der Benutzer muss den Prozess bearbeiten können.
        return !$environment->processVersion->isPublished() && $user->can('update', $process);
    }

    /**
     * Eine Simulationsumgebung löschen dürfen
     * @param User $user
     * @param Environment $environment
     * @return bool
     */
    public function delete(User $user, Environment $environment) {
        $process = $environment->processVersion->process;

        if (is_null($process)) {
            return false;
        }

        // Der Benutzer muss den Prozess bearbeiten können.
        return !$environment->processVersion->isPublished() && $user->can('update', $process);
    }

}
