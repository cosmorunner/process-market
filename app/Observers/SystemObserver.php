<?php

namespace App\Observers;

use App\Models\System;

/**
 * Class ProcessObserver
 * @package App\Observers
 */
class SystemObserver {

    /**
     * Handle the Process "deleting" event.
     *
     * @param System $system
     * @return void
     */
    public function deleting(System $system) {
        // Alle Simulationen löschen
        $system->synchronizations()->delete();
    }

}
