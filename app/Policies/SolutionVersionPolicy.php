<?php

namespace App\Policies;

use App\Models\SolutionVersion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SolutionVersionPolicy
 * @package App\Policies
 */
class SolutionVersionPolicy {

    use HandlesAuthorization;

    /**
     * Prüfung ob der Benutzer eine Lösungs-Version bearbeiten darf.
     * @param User $user
     * @param SolutionVersion $solutionVersion
     * @return bool
     */
    public function update(User $user, SolutionVersion $solutionVersion) {
        $solution = $solutionVersion->solution;

        if (is_null($solution)) {
            return false;
        }

        // Der Benutzer muss die Solution bearbeiten können und es darf keine Simulation aktiv sein.
        return $user->can('update', $solution);
    }

    /**
     * Prüfung ob die View zum "Fertigstellen" angezeigt werden darf.
     * @param User $user
     * @param SolutionVersion $solutionVersion
     * @return bool
     */
    public function complete(User $user, SolutionVersion $solutionVersion) {
        $solution = $solutionVersion->solution;

        if (is_null($solution)) {
            return false;
        }

        // Der Benutzer muss die Solution bearbeiten können und es darf keine Simulation aktiv sein.
        return $user->can('complete', $solution);
    }

    /**
     * Prüfung ob die View zum "Synchronisieren" angezeigt werden darf.
     * @param User $user
     * @param SolutionVersion $solutionVersion
     * @return bool
     */
    public function sync(User $user, SolutionVersion $solutionVersion) {
        $solution = $solutionVersion->solution;

        if (is_null($solution)) {
            return false;
        }

        // Der Benutzer muss die Solution bearbeiten können und es darf keine Simulation aktiv sein.
        return $user->can('sync', $solution);
    }

}
