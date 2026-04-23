<?php

namespace App\ProcessType\Commands;

use App\Enums\ProcessRolePermissions;
use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Permission;
use App\ProcessType\StatusRule;
use App\ProcessType\StatusType;

/**
 * Class DeleteState
 * @package App\ProcessType\Commands
 */
class DeleteStatusType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $statusType = $this->definition->statusType($this->payload['id']);
        $statusTypeId = $statusType->id;

        $this->definition->statusTypes = $this->definition->statusTypes
            ->filter(fn(StatusType $statusType) => $statusType->id !== $statusTypeId)->values();

        return $this->definition;
    }

    /**
     * Bevor der Statustyp entfernt werden darf müssen die Zustände entfernt werden.
     * @param Definition $definition
     * @return Command[]
     */
    protected function beforeExecutingCommands(Definition $definition): array {
        $statusType = $this->definition->statusType($this->payload['id']);
        $statusTypeId = $statusType->id;
        $commands = [];

        foreach ($statusType->states as $state) {
            $commands[] = new DeleteState([
                'status_type_id' => $statusTypeId,
                'state_id' => $state->id
            ], $this->definition, $this->processVersion);
        }

        return $commands;
    }

    /**
     * Zusätzlich das Recht zu dem Statustyp von allen Rollen entfernen.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $commands = [];

        // Berechtung von allen Rollen löschen.
        $commands[] = new DeletePermissionFromAllRoles([
            'ident' => ident(ProcessRolePermissions::ViewStatustype->value, $this->payload['id'])
        ], $updatedDefinition, $this->processVersion);

        // Gelöschte Statustyp-Id
        $statusTypeId = $this->payload['id'];

        // Aktions- und Statusregeln von diesem Statustyp löschen.
        $updatedDefinition->actionTypes->each(function (ActionType $actionType) use (&$commands, $updatedDefinition, $statusTypeId) {
            $actionType->actionRules->each(function (ActionRule $actionRule) use (&$commands, $updatedDefinition, $statusTypeId) {

                if ($actionRule->type === ActionRule::TYPE_STATUS && $actionRule->status_type_id === $statusTypeId) {
                    $commands[] = new DeleteActionRule([
                        'action_type_id' => $actionRule->action_type_id,
                        'status_type_id' => $statusTypeId
                    ], $updatedDefinition, $this->processVersion);
                }
            });

            $actionType->statusRules->each(function (StatusRule $statusRule) use (&$commands, $updatedDefinition, $statusTypeId) {

                if ($statusRule->status_type_id === $statusTypeId && $statusRule->operator === StatusRule::OPERATOR_SET) {
                    $commands[] = new DeleteStatusRule([
                        'action_type_id' => $statusRule->action_type_id,
                        'status_type_id' => $statusRule->status_type_id
                    ], $updatedDefinition, $this->processVersion);
                }
            });
        });

        // Sortierung neu setzten, damit keine Lücken bei den Statustypen entstehen.
        $items = $updatedDefinition->statusTypes->sortBy('sort')->each(fn(StatusType $statusType, $index) => $statusType->sort = $index);
        $commands[] = new UpdateStatusTypes(['items' => $items->toArray()], $updatedDefinition, $this->processVersion);

        return $commands;
    }

}
