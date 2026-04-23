<?php

namespace App\Environment\Commands;

use App\Environment\Process;
use App\Models\Environment;

/**
 * Class StoreProcess
 * @package App\Environment\Commands
 */
class StoreProcess extends Command {

    /**
     * Erstellen eines Prozesses.
     * @return Environment
     */
    public function command(): Environment {
        $processes = $this->environment->blueprint->processes;
        $processes->add(Process::make($this->payload));

        $this->environment->updateBlueprint('processes', $processes);

        return $this->environment;
    }
}
