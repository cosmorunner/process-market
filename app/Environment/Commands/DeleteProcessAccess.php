<?php

namespace App\Environment\Commands;

use App\Environment\Process;
use App\Models\Environment;

/**
 * Delete access for a user or a group from all processes accesses.
 * Class DeleteProcessAccess
 * @package App\Environment\Commands
 */
class DeleteProcessAccess extends Command {

    /**
     * @return Environment
     */
    public function command(): Environment {
        // Can be user id, user email or group id.
        $modelId = $this->payload['accessible_model_id'];

        $processes = $this->environment->blueprint->processes->map(function (Process $process) use ($modelId) {
            unset($process->accesses[$modelId]);

            return $process;
        });
        $this->environment->updateBlueprint('processes', $processes);

        return $this->environment;
    }
}
