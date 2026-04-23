<?php

namespace App\Policies;

use App\Models\Demo;
use App\Models\Organisation;
use App\Models\SolutionVersion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DemoPolicy
 * @package App\Policies
 */
class DemoPolicy {

    use HandlesAuthorization;

    /**
     * Anzeigen einer laufenden Demo. Kann nur durch die Person, die die Demo gestartet hat, geöffnet werden.
     * @param User $user
     * @param Demo $demo
     * @return bool
     */
    public function view(User $user, Demo $demo) {
        $solutionAuthor = $demo->solution->author;

        // Benutzer hat die Demo erstellt (Standard Prozess Demo oder "Benutzer-Demo" bei einer Lösung
        if ($demo->user_id === $user->id) {
            return true;
        }

        // ...oder  der Benutzer der Author der Lösung...
        if ($solutionAuthor->id === $user->id) {
            return true;
        }

        // oder der Benutzer kann "demos.view" in der Organisation der Lösung.
        if ($solutionAuthor instanceof Organisation && $user->roleWithin($solutionAuthor)?->can('demos.view')) {
            return true;
        }

        return false;
    }

    /**
     * Starten einer Demo. Entweder dem Benutzer gehört die Lösung, oder der Benutzer ist Teil einer Organisation der die Lösung gehört, oder
     * der Benutzer/die Organisation hat eine Lizenz für die Lösung.
     * @param User $user
     * @param SolutionVersion $solutionVersion
     * @return bool
     */
    public function create(User $user, SolutionVersion $solutionVersion) {
        return $user->authored($solutionVersion->solution) ||
            $user->roleWithin($solutionVersion->solution->author)?->can('demos.view') ||
            $solutionVersion->solution->isPubliclyAccessible() && !$solutionVersion->hasPrivateProcess();
    }

    /**
     * Prüfen ob der Benutzer die Demo ausführen oder beenden darf.
     * @param User $user
     * @param Demo|null $demo
     * @return bool
     */
    public function update(User $user, Demo $demo = null) {
        return $this->create($user, $demo->solutionVersion);
    }

    /**
     * Beenden/Löschen einer laufenden Demo.
     * @param User $user
     * @param Demo $demo
     * @return bool
     */
    public function delete(User $user, Demo $demo) {
        return $this->create($user, $demo->solutionVersion);
    }
}
