<?php

namespace App\Environment\Commands;

use App\Environment\Connector;
use App\Environment\Request;
use App\Models\Environment;

/**
 * Class DeleteConnector
 * @package App\Environment\Commands
 */
class DeleteConnector extends Command {

    /**
     * Löschen eines Connectors.
     * @return Environment
     */
    public function command(): Environment {
        $connectors = $this->environment->blueprint->connectors->filter(fn(Connector $connector) => $connector->id !== $this->payload['id'] ?? null);
        $requests = $this->environment->blueprint->requests->filter(fn(Request $request) => $request->connector_id !== $this->payload['id'] ?? null);

        $this->environment->updateBlueprint('connectors', $connectors);
        $this->environment->updateBlueprint('requests', $requests);

        return $this->environment;
    }
}
