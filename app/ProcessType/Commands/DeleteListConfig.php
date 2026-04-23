<?php

namespace App\ProcessType\Commands;

use App\Enums\ProcessRolePermissions;
use App\ProcessType\Definition;
use App\ProcessType\ListConfig;

/**
 * Class DeleteListConfig
 * @package App\ProcessType\Commands
 */
class DeleteListConfig extends Command {

    /**
     * Definition array keys that are updated by the command.
     * Only every key is returned after the command, for improved performance.
     * If empty, everything is returned.
     * @var array
     */
    const AFFECTS_PARTS = ['list_configs'];

    /**
     * Executes a command. Is used to edit the graph definition.
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->listConfigs = $this->definition->listConfigs
            ->filter(fn(ListConfig $listConfig) => $listConfig->id !== $this->payload['id'])->values();

        return $this->definition;
    }

    /**
     * In addition, remove the right to the list configuration from all roles.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        return [new DeletePermissionFromAllRoles([
            'ident' => ident(ProcessRolePermissions::ViewListConfig->value, $this->payload['id'])
        ], $updatedDefinition, $this->processVersion)];
    }

}
