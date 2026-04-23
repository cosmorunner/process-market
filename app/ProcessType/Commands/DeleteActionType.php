<?php


namespace App\ProcessType\Commands;

use App\Enums\ProcessRolePermissions;
use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Permission;
use App\Traits\DeletesProcessorModelUsage;

/**
 * Class DeleteActionType
 * @package App\ProcessType\Commands
 */
class DeleteActionType extends Command {

    use DeletesProcessorModelUsage;

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return mixed
     */
    public function command(): Definition {
        $actionType = $this->definition->actionType($this->payload['id']);
        $actionTypeId = $actionType->id;

        // Aktionstyp entfernen
        $this->definition->actionTypes = $this->definition->actionTypes
            ->filter(fn(ActionType $actionType) => $actionType->id !== $actionTypeId)
            ->values();

        return $this->definition;
    }

    /**
     * Nach dem Löschen eines Aktionstyps muss die Nutzung des Aktionstyps in allen Prozessoren
     * aller Aktionstypen entfernt werden und das Recht zu dem Aktionstyp von allen Rollen entfernt werden.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $commands = $this->deleteProcessorModelUsages($updatedDefinition, 'actionType', $this->payload['id']);

        $deletePermissionFromAllRoles = new DeletePermissionFromAllRoles([
            'ident' => ident(ProcessRolePermissions::ExecuteActiontype->value, $this->payload['id'])
        ], $updatedDefinition, $this->processVersion);

        return array_merge($commands, [$deletePermissionFromAllRoles]);
    }

    /**
     * Method called after the command was executed. Is only called on the "main" command, not called on commands that are
     * added via the "beforeExecutingCommands" and "afterExecutingCommands".
     * @param ProcessVersion $processVersion Updated process version after original command execution.
     * "definition" was already updated in the database at this point.
     * @param array $payload Original payload of the command.
     * @return void
     */
    public function afterCommandExecution(ProcessVersion $processVersion, array $payload):void {
        $actionTypeId = $payload['id'] ?? '';

        foreach ($processVersion->environments as $environment) {
            if ($actionTypeId && $environment->initial_action_type_id === $actionTypeId) {
                $environment->update([
                    'initial_action_type_id' => '',
                    'query_context' => ''
                ]);
            }
        }
    }

}
