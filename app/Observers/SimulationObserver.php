<?php

namespace App\Observers;

use App\Models\Simulation;
use App\SimulationConnector;
use Throwable;

/**
 * Class SimulationObserver
 * @package App\Observers
 */
class SimulationObserver {

    /**
     * Handle the Process "deleting" event.
     * @param Simulation $simulation
     * @return void
     */
    public function deleting(Simulation $simulation) {
        // If the process (and therefore all simulations) is deleted/archived, all running simulations are removed.
        if ($simulation->isRunning()) {
            $connector = new SimulationConnector($simulation);

            try {
                $connector->deleteAllisaSimulation();
            }
            catch (Throwable) {
                // Ignore
            }
        }
    }

}
