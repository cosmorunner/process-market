<?php /** @noinspection PhpUnusedParameterInspection */

namespace App\Policies;

use App\Models\Solution;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SolutionPolicy
 * @package App\Policies
 */
class SolutionPolicy {

    use HandlesAuthorization;

    /**
     * Prüfung ob der Benutzer oder anonyme Nutzer eine Demo von der Solution starten darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     * @noinspection PhpUnused
     */
    public function start_demo(User $user, Solution $solution) {
        return $solution->isPubliclyAccessible() || $user->authored($solution) || $user->roleWithin($solution->author)?->can('demos.view');
    }

    /**
     * Prüfung ob der Benutzer die Lösung einsehen darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     */
    public function view(User $user, Solution $solution) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('solutions.view');
    }

    /**
     * Prüfung ob der Benutzer eine Lösungs-Lizenz erwerben darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     */
    public function purchase(User $user, Solution $solution) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('licenses.manage');
    }

    /**
     * Prüfung ob der Benutzer die Solution bearbeiten darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     */
    public function update(User $user, Solution $solution) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('solutions.update');
    }

    /**
     * Prüfung ob der Benutzer eine Lösungs-Version zu einer Allisa Plattform exportieren darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     */
    public function sync(User $user, Solution $solution) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('solution_versions.export');
    }

    /**
     * Prüfung ob der Benutzer den eine Prozess-Version fertigstellen darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     */
    public function complete(User $user, Solution $solution) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('solution_versions.create');
    }

    /**
     * Prüfung ob der Benutzer die Solution archivieren darf.
     * @param User $user
     * @param Solution $solution
     * @return bool
     */
    public function delete(User $user, Solution $solution) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('solutions.delete');
    }

    /**
     * Der Benutzer darf die Lizenzen von sich selbst und von den Organisation einsehen, bei denen er Mitglied ist.
     * @param User $user
     * @param Solution $solution
     * @param string $ownerNamespace
     * @return bool
     */
    public function view_licenses(User $user, Solution $solution, string $ownerNamespace) {
        return $user->authored($solution) || $user->roleWithin($solution->author)?->can('licenses.view');
    }
}
