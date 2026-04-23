<?php

namespace App\Environment\Commands;

use App\Environment\Variable;
use App\Models\Environment;

/**
 * Class StoreVariable
 * @package App\Environment\Commands
 */
class StoreVariable extends Command {

    /**
     * Erstellen eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $variables = $this->environment->blueprint->variables;
        $variables->add(Variable::make($this->payload));
        $this->environment->updateBlueprint('variables', $variables);

        return $this->environment;
    }
}
