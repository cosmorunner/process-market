<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateProcessTypeUnique
 * @package App\ProcessType\Commands
 */
class UpdateProcessTypeUnique extends Command {

    /**
     * Flag indicating whether the graph must be recalculated after the command. For example, for StoreActionType or DeleteActionRule
     * the graph must be recalculated.
     * @var bool
     */
    public $recalculate = true;

    /**
     * Update unique rules
     * @return Definition
     */
    public function command(): Definition {
        $type = $this->payload['type'];
        $data = $this->payload['data'];

        $this->definition->unique[$type] = $data;

        return $this->definition;
    }
}
