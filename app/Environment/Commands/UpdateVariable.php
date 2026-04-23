<?php

namespace App\Environment\Commands;

use App\Environment\Variable;
use App\Models\Environment;

/**
 * Class UpdateVariable
 * @package App\Environment\Commands
 */
class UpdateVariable extends Command {

    /**
     * Aktualisieren eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $updatedVariable = Variable::make($this->payload);
        $variables = $this->environment->blueprint->variables;
        $variables = $variables->map(fn(Variable $variable) => $variable->id === $updatedVariable->id ? $updatedVariable : $variable);

        $this->environment->updateBlueprint('variables', $variables);

        return $this->environment;
    }
}
