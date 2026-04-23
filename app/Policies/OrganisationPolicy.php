<?php /** @noinspection PhpUnused */

namespace App\Policies;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy für den Zugriff auf die Profil-Seite einer Organisation.
 * Class OrganisationPolicy
 * @package App\Policies
 */
class OrganisationPolicy {

    use HandlesAuthorization;

    /**
     * Einsehen der Profil-Seite einer Organisation.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function viewProfile(User $user, Organisation $organisation) {
        return $user->hasAccessTo($organisation);
    }

    /**
     * Prüft ob der Benutzer den Account der Organisation verwalten kann (Administrator-Rechte).
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function manageAccount(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'organisation.manage');
    }

    /**
     * Einsehen der Lizenzen-Seite einer Organisation.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function viewLicenses(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'licenses.view');
    }

    /**
     * Prüft ob der Benutzer die Lizenzen der Organisation verwalten kann.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function manageLicenses(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'licenses.manage');
    }

    /**
     * Einsehen der Mitglieder-Seite einer Organisation.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function viewMembers(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'members.view');
    }

    /**
     * Prüft ob der Benutzer die Mitglieder der Organisation verwalten kann.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function manageMembers(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'members.manage');
    }

    /**
     * Prüft ob der Benutzer die Mitglieder der Organisation verwalten kann.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function manageAdmins(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'members.admins');
    }

    /**
     * Prüft ob der Benutzer die Allisa Plattformen der Organisation verwalten kann.
     * @param User $user
     * @param Organisation $organisation
     * @return bool
     */
    public function managePlatforms(User $user, Organisation $organisation) {
        return $user->hasOrganisationPermission($organisation, 'platforms.manage');
    }

    /**
     * Prüft ob der Benutzer einen bestimmtes Mitglied der Organisation bearbeiten darf.
     * Nur möglich wenn der eingeloggte Benutzer Administrator ist und der zu bearbeitende Benutzer NICHT Administrator ist oder man selbst.
     * @param User $loggedInUser
     * @param Organisation $organisation
     * @param User $user
     * @return bool
     */
    public function manageMember(User $loggedInUser, Organisation $organisation, User $user) {
        if (!$this->manageMembers($loggedInUser, $organisation)) {
            return false;
        }

        $role = $user->roleWithin($organisation);
        $loggedInUserRole = $loggedInUser->roleWithin($organisation);

        // Wenn der zu bearbeitende Benutzer kein Admin ist oder der zu bearbeitende Benutzer der eingeloggte Benutzer ist.
        if ($role instanceof Role && (!$role->isAdmin() || $loggedInUserRole->isOwner()) || $user->id === $loggedInUser->id) {
            return true;
        }

        return false;
    }
}
