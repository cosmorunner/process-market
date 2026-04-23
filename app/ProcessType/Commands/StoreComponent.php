<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\ActionTypeComponent;
use App\ProcessType\Definition;

/**
 * Class StoreComponent
 * @package App\ProcessType\Commands
 */
class StoreComponent extends Command {

    /**
     * Definition array keys that are updated by the command.
     * Only every key is returned after the command, for improved performance.
     * If empty, everything is returned.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    /**
     * Executes a command. Is used to edit the graph definition.
     * @return Definition
     */
    public function command(): Definition {
        $component = ActionTypeComponent::make($this->payload);
        $actionType = $this->definition->actionType($component->action_type_id);

        $sort = $actionType->components->max('sort') + 1;
        $this->payload['sort'] = $sort;
        $component->sort = $sort;

        if ($actionType instanceof ActionType) {
            $actionType->components->add($component);
        }

        return $this->definition;
    }

}
