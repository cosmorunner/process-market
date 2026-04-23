<?php

namespace App\Environment\Commands;

use App\Environment\Request;
use App\Models\Environment;

/**
 * Class DeleteRequest
 * @package App\Environment\Commands
 */
class DeleteRequest extends Command {

    /**
     * Löschen eines Connector-Requests.
     * @return Environment
     */
    public function command(): Environment {
        $requests = $this->environment->blueprint->requests->filter(fn(Request $request) => $request->id !== $this->payload['id'] ?? null);
        $this->environment->updateBlueprint('requests', $requests);

        return $this->environment;
    }
}
