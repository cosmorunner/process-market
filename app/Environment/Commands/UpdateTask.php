<?php

namespace App\Environment\Commands;

use App\Environment\Task;
use App\Models\Environment;

/**
 * Class UpdateTask
 * @package App\Environment\Commands
 */
class UpdateTask extends Command {

    /**
     * Aktualisieren eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $updatedTask = Task::make($this->payload);
        $tasks = $this->environment->blueprint->tasks;
        $tasks = $tasks->map(fn(Task $task) => $task->id === $updatedTask->id ? $updatedTask : $task);

        $this->environment->updateBlueprint('tasks', $tasks);

        return $this->environment;
    }
}
