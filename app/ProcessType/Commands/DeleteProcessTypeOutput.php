<?php

namespace App\ProcessType\Commands;

use App\Enums\ProcessRolePermissions;
use App\ProcessType\Definition;
use App\ProcessType\Output;

/**
 * Class DeleteProcessTypeOutput
 * @package App\ProcessType\Commands
 */
class DeleteProcessTypeOutput extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['outputs'];

    public $recalculate = false;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $outputs = $this->definition->outputs->filter(fn(Output $output) => $output->name !== $this->payload['name'])->values();
        $this->definition->outputs = $outputs;

        return $this->definition;
    }

    /**
     * Zusätzlich das Recht zu dem Output von allen Rollen entfernen.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        return [new DeletePermissionFromAllRoles([
            'ident' => ident(ProcessRolePermissions::ViewOutput->value, $this->payload['name'])
        ], $updatedDefinition, $this->processVersion)];
    }

}
