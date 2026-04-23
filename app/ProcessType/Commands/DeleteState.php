<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\State;
use App\ProcessType\StatusRule;
use Ramsey\Uuid\Uuid;

/**
 * Class DeleteState
 * @package App\ProcessType\Commands
 */
class DeleteState extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    /**
     * Zustand der gelöscht wird.
     * @var State
     */
    private State $state;

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $statusType = $this->definition->statusType($this->payload['status_type_id']);
        $this->state = $statusType->state($this->payload['state_id']);
        $states = $statusType->states->filter(fn(State $state) => $state->id !== $this->payload['state_id'])->values();
        $statusType->states = $states;

        return $this->definition;
    }

    /**
     * Zustände bei Aktionsregeln entfernen. Aktionsregel gegebenenfalls löschen, falls der Zustand der einzige war.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    protected function afterExecutingCommands(Definition $updatedDefinition): array {
        $commands = [];

        $updatedDefinition->actionTypes->each(function (ActionType $actionType) use (&$commands, $updatedDefinition) {
            // Aktionsregeln aktualisieren oder löschen
            $actionType->actionRules->each(function (ActionRule $actionRule) use (&$commands, $updatedDefinition) {
                if ($actionRule->type !== ActionRule::TYPE_STATUS || $actionRule->status_type_id !== $this->payload['status_type_id']) {
                    return;
                }

                if ($actionRule->usesStates()) {
                    // Zustand aus Regel entfernen
                    $actionRule->state_ids = array_values(array_filter($actionRule->state_ids, fn($ele) => $ele !== $this->state->id));

                    // Falls es der letzte Zustand war, wird die Aktionsregel entfernt...
                    if (empty($actionRule->state_ids)) {
                        $commands[] = new DeleteActionRule([
                            'action_type_id' => $actionRule->action_type_id,
                            'status_type_id' => $this->payload['status_type_id']
                        ], $updatedDefinition, $this->processVersion);
                    }
                    // Andernfalls wird die Aktionsregel nur aktualisiert.
                    else {
                        $commands[] = new UpdateActionRule($actionRule->toArray(), $updatedDefinition, $this->processVersion);
                    }
                }
            });

            // Statusregeln gegebenenfalls löschen
            $actionType->statusRules->each(function (StatusRule $statusRule) use (&$commands, $updatedDefinition) {
                // Auf Statustyp und "setzen"-Operator prüfen
                if ($statusRule->status_type_id !== $this->payload['status_type_id'] || $statusRule->operator !== StatusRule::OPERATOR_SET) {
                    return;
                }

                // Nutzung eines Zustandes
                // Statusregel löschen wenn diese auf den Zustand setzt.
                if ($statusRule->state === $this->state->id) {
                    $commands[] = new DeleteStatusRule([
                        'action_type_id' => $statusRule->action_type_id,
                        'status_type_id' => $statusRule->status_type_id
                    ], $updatedDefinition, $this->processVersion);
                }

                // Manueller Wert
                // Statusregel löschen wenn diese auf den gelöschten Zustand setzen würde.
                // Statusregel Wert muss im Wertebereich liegen.
                if (count($statusRule->values) && ActionRule::operatorInBetween($statusRule->values[0], $this->state->min, $this->state->max)) {
                    $commands[] = new DeleteStatusRule([
                        'action_type_id' => $statusRule->action_type_id,
                        'status_type_id' => $statusRule->status_type_id
                    ], $updatedDefinition, $this->processVersion);
                }

                // Konditionen
                if (count($statusRule->conditions)) {
                    $filtered = array_filter($statusRule->conditions, function ($condition) {
                        // Konditionen löschen die den gelöschten Zustand nutzen.
                        if (Uuid::isValid($condition[0])) {
                            return $condition[0] !== $this->state->id;
                        }
                        // Konditionen löschen die auf einen Wert setzen der im Bereich des gelöschten Zustandes liegt.
                        if (State::isValidValueFormat($condition[0])) {
                            return !ActionRule::operatorInBetween($condition[0], $this->state->min, $this->state->max);
                        }

                        return true;
                    });

                    $statusRule->conditions = array_values($filtered);

                    // Falls alle Konditionen gelöscht wurden, die Statusregel löschen
                    if (count($statusRule->conditions) === 0) {
                        $commands[] = new DeleteStatusRule([
                            'action_type_id' => $statusRule->action_type_id,
                            'status_type_id' => $statusRule->status_type_id
                        ], $updatedDefinition, $this->processVersion);
                    }
                }
            });
        });

        return $commands;
    }

}
