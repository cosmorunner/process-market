<?php

namespace App\Environment\Commands;

use App\Environment\Task;
use App\Models\Environment;

/**
 * Class DeleteTask
 * @package App\Environment\Commands
 */
class DeleteTask extends Command {

    /**
     * Löschen eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $tasks = $this->environment->blueprint->tasks->filter(fn(Task $task) => $task->identifier !== $this->payload['identifier'] ?? null);
        $this->environment->updateBlueprint('tasks', $tasks);

        return $this->environment;
    }
}
