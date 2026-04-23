<?php

namespace App\Environment\Commands;

use App\Environment\Process;
use App\Environment\Relation;
use App\Models\Environment;

/**
 * Class DeleteProcess
 * @package App\Environment\Commands
 */
class DeleteProcess extends Command {

    /**
     * Löschen eines Prozesses
     * @return Environment
     */
    public function command(): Environment {
        $processes = $this->environment->blueprint->processes->filter(fn(Process $process) => $process->id !== $this->payload['id'] ?? null);
        $relations = $this->environment->blueprint->relations->filter(fn(Relation $relation) => $relation->left !== $this->payload['id'] && $relation->right !== $this->payload['id']);
        $this->environment->updateBlueprint('processes', $processes);
        $this->environment->updateBlueprint('relations', $relations);

        return $this->environment;
    }
}
