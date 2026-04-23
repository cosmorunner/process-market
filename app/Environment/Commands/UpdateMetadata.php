<?php

namespace App\Environment\Commands;

use App\Environment\Process;
use App\Models\Environment;

/**
 * Class UpdateMetadata
 * @package App\Environment\Commands
 */
class UpdateMetadata extends Command {

    /**
     * Aktualisieren der Umgebung-Metadaten.
     * @return Environment
     */
    public function command(): Environment {
        $processes = $this->environment->blueprint->processes;
        $processes->add(Process::make($this->payload));

        $this->environment->updateBlueprint('processes', $processes);

        return $this->environment;
    }
}
