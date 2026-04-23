<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\State;
use App\ProcessType\StatusRule;
use App\ProcessType\StatusType;

/**
 * Class UpdateState
 * @package App\ProcessType\Commands
 */
class UpdateState extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    public $recalculate = true;

    private State $oldState;
    private State $updatedState;

    /**
     * Statustyp aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $statusType = $this->definition->statusType($this->payload['status_type_id']);

        if (!$statusType instanceof StatusType) {
            return $this->definition;
        }

        // Make sure, min and max ist set.
        if (!array_key_exists('max', $this->payload) && array_key_exists('min', $this->payload)) {
            $this->payload['max'] = $this->payload['min'];
        }

        $statusType->states = $statusType->states->map(function (State $state) use ($statusType) {
            if ($state->id === $this->payload['id']) {
                $this->oldState = $state;
                $this->updatedState = State::make([...$state->toArray(), ...$this->payload]);

                return $this->updatedState;
            }

            return $state;
        });

        return $this->definition;
    }

    /**
     * Gibt zusätzliche Commands zurück die ebenfalls nach der Command-Ausführung ausgeführt werden sollen.
     * @param Definition $updatedDefinition Die bereits durch den ursprünglichen Command aktualisierte Definition
     * @return Command[]
     */
    protected function afterExecutingCommands(Definition $updatedDefinition): array {
        $commands = [];

        foreach ($updatedDefinition->actionTypes as $actionType) {
            foreach ($actionType->statusRules as $statusRule) {
                if ($statusRule->status_type_id !== $this->payload['status_type_id']) {
                    continue;
                }

                // Falls sich Zustand so verändert hat, dass es nun einen Wertebereich gibt, z.B. 1-1 --> 1-3,
                // müssen alle Statusregeln, die eine "state"-Statusregel ("state"-Attribut) haben gelöscht werden.
                // Es gibt eine Statusregel die direkt auf den geänderten Zustand setzt, welcher nun einen
                // Wertebereich hat.
                if ($statusRule->state && $this->updatedState->min !== $this->updatedState->max) {
                    $commands[] = (new DeleteStatusRule([
                        'action_type_id' => $actionType->id,
                        'status_type_id' => $this->payload['status_type_id']
                    ], $updatedDefinition, $this->processVersion));
                }

                // Falls sich der Zustand so verändert hat, dass keinen Wertebereich mehr gibt, z.B. 1-3 --> 1-1,
                // werden automatisch alle "manuellen", "set" Statusregeln so verändert, dass sie direkt auf den Zustand zeigen.
                if ($statusRule->operator === StatusRule::OPERATOR_SET
                    && $this->updatedState->min === $this->updatedState->max
                    && $this->oldState->min !== $this->oldState->max) {
                    $commands[] = (new UpdateStatusRule(array_merge($statusRule->toArray(), [
                        'values' => [],
                        'state' => $this->updatedState->id
                    ]), $updatedDefinition, $this->processVersion));
                }
            }
        }

        return $commands;
    }

}
