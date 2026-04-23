<?php

namespace App\Observers;

use App\Models\Process;
use App\Models\Simulation;
use App\Models\User;

/**
 * Class ProcessObserver
 * @package App\Observers
 */
class ProcessObserver {

    /**
     * Prozess wird archiviert
     * @param Process $process
     * @return void
     */
    public function deleting(Process $process) {
        // Alle Simulationen löschen, unabhängig davon, ob der Prozess archiviert wird oder forceDeleted wird.
        $process->simulations->each(fn(Simulation $simulation) => $simulation->delete());

        // Alle Prozess-Versionen löschen, wenn es ein forceDelete ist
        if ($process->isForceDeleting()) {
            foreach ($process->versions as $version) {
                $version->history()->delete();
                $version->delete();
            }
        }

        // Cache von Benutzern löschen, die darauf Zugriff haben.
        $process->accessibleUsers()->map(fn(User $user) => $user->flushCache());
    }

    /**
     * Wenn der Prozess aktualisiert wurde.
     * @param Process $process
     * @return void
     */
    public function updating(Process $process) {
        $process->accessibleUsers()->map(function (User $user) {
            $user->flushCache();
        });
    }

}
