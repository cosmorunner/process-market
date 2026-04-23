<?php

namespace App\Environment\Commands;

use App\Environment\Process;
use App\Models\Environment;

/**
 * Class UpdateProcess
 * @package App\Environment\Commands
 */
class UpdateProcess extends Command {

    /**
     * Aktualisieren eines Prozesses.
     * @return Environment
     */
    public function command(): Environment {
        $updatedProcess = Process::make($this->payload);
        $processes = $this->environment->blueprint->processes;
        $processes = $processes->map(fn(Process $process) => $process->id === $updatedProcess->id ? $updatedProcess : $process);

        $this->environment->updateBlueprint('processes', $processes);

        return $this->environment;
    }
}
