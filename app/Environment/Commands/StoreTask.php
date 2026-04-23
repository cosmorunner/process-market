<?php

namespace App\Environment\Commands;

use App\Environment\Task;
use App\Models\Environment;

/**
 * Class StoreTask
 * @package App\Environment\Commands
 */
class StoreTask extends Command {

    /**
     * Erstellen eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $tasks = $this->environment->blueprint->tasks;
        $tasks->add(Task::make($this->payload));
        $this->environment->updateBlueprint('tasks', $tasks);

        return $this->environment;
    }
}
