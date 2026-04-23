<?php

namespace App\Observers;

use App\DemoConnector;
use App\Models\Demo;
use Throwable;

/**
 * Class DemoObserver
 * @package App\Observers
 */
class DemoObserver {

    /**
     * Handle the Process "deleting" event.
     *
     * @param Demo $demo
     * @return void
     */
    public function deleting(Demo $demo) {
        // Nur bei laufenden Demos kann die Allisa Simulations-Instanz gelöscht werden,
        // andernfalls existiert sie ohnehin nicht mehr.
        if ($demo->isRunning()) {
            $connector = new DemoConnector($demo);

            try {
                $connector->deleteAllisaLiveDemo();
            }
            catch (Throwable) {
                // Ignore
            }
        }
    }

}
