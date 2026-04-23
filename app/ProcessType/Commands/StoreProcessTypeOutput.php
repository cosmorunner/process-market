<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Output;

/**
 * Class StoreProcessTypeOutput
 * @package App\ProcessType\Commands
 */
class StoreProcessTypeOutput extends Command {

    /**
     * Definition array keys that are updated by the command.
     * Only every key is returned after the command, for improved performance.
     * If empty, everything is returned.
     * @var array
     */
    const AFFECTS_PARTS = ['outputs'];

    public $recalculate = false;

    /**
     * Creates a process type output.
     * @return Definition
     */
    public function command(): Definition {
        $model = Output::make($this->payload);

        if ($model instanceof Output) {
            $this->definition->outputs->add($model);
        }

        return $this->definition;
    }

}
