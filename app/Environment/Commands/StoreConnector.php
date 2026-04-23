<?php

namespace App\Environment\Commands;

use App\Environment\Connector;
use App\Models\Environment;

/**
 * Class StoreConnector
 * @package App\Environment\Commands
 */
class StoreConnector extends Command {

    /**
     * Erstellen eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $connectors = $this->environment->blueprint->connectors;
        $connectors->add(Connector::make($this->payload));

        $this->environment->updateBlueprint('connectors', $connectors);

        return $this->environment;
    }
}
