<?php

namespace App\Environment\Commands;

use App\Environment\Connector;
use App\Models\Environment;

/**
 * Class UpdateConnector
 * @package App\Environment\Commands
 */
class UpdateConnector extends Command {

    /**
     * Aktualisieren eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $updatedConnector = Connector::make($this->payload);
        $connectors = $this->environment->blueprint->connectors;
        $connectors = $connectors->map(fn(Connector $connector) => $connector->id === $updatedConnector->id ? $updatedConnector : $connector);

        $this->environment->updateBlueprint('connectors', $connectors);

        return $this->environment;
    }
}
