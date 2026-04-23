<?php

namespace App\Observers;

use App\Models\Demo;
use App\Models\Solution;
use App\Models\User;

/**
 * Class SolutionObserver
 * @package App\Observers
 */
class SolutionObserver {

    /**
     * Handle the Process "deleting" event.
     *
     * @param Solution $solution
     * @return void
     */
    public function deleting(Solution $solution) {
        // Alle Versionen und Demos löschen, wenn der Prozess "forceDeleted" (keine Archivierung) wird.
        if ($solution->isForceDeleting()) {
            $solution->versions()->delete();

            // Alle Demos
            $solution->demos->each(fn(Demo $demo) => $demo->delete());
        }
        // Ansonsten lediglich die Benutzer-Demos löschen.
        else {
            // Falls der Prozess nur soft-deleted (archiviert) wird, werden KEINE Admin-Demos gelöscht.
            $solution->demos->filter(fn(Demo $demo) => $demo->isUserDemo())->each(fn(Demo $demo) => $demo->delete());
        }

        // Cache von Benutzern löschen, die darauf Zugriff haben.
        $solution->accessibleUsers()->map(fn(User $user) => $user->flushCache());
    }

}
