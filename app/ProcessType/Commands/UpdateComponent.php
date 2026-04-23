<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionTypeComponent;
use App\ProcessType\Definition;

/**
 * Class UpdateActionTypeComponentOptions
 * @package App\ProcessType\Commands
 */
class UpdateComponent extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    /**
     * Flagge, ob nach dem Command der Graph neu berechnet werden muss. Z.B. bei StoreActionType oder DeleteActionRule
     * muss der Graph neu berechnet werden.
     * @var bool
     */
    public $recalculate = true;

    /**
     * Aktionstyp aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $componentId = $this->payload['id'];
        $actionTypeId = $this->payload['action_type_id'];

        $actionType = $this->definition->actionType($actionTypeId);
        $actionType->components = $actionType->components->map(function (ActionTypeComponent $component) use ($componentId) {
            if ($component->id === $componentId) {
                return ActionTypeComponent::make($this->payload);
            }

            return $component;
        });

        return (new UpdateActionType($actionType->toArray(), $this->definition, $this->processVersion))->execute();
    }

}
