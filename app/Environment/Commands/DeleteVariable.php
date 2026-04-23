<?php

namespace App\Environment\Commands;

use App\Environment\Variable;
use App\Models\Environment;

/**
 * Class DeleteVariable
 * @package App\Environment\Commands
 */
class DeleteVariable extends Command {

    /**
     * Löschen eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $variables = $this->environment->blueprint->variables->filter(fn(Variable $variable) => $variable->identifier !== $this->payload['identifier'] ?? null);
        $this->environment->updateBlueprint('variables', $variables);

        return $this->environment;
    }
}
