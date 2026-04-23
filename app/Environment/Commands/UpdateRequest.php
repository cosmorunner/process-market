<?php

namespace App\Environment\Commands;

use App\Environment\Request;
use App\Models\Environment;

/**
 * Class UpdateRequest
 * @package App\Environment\Commands
 */
class UpdateRequest extends Command {

    /**
     * Aktualisieren eines Connector-Requests
     * @return Environment
     */
    public function command(): Environment {
        $updatedRequest = Request::make($this->payload);
        $requests = $this->environment->blueprint->requests;
        $requests = $requests->map(fn(Request $request) => $request->id === $updatedRequest->id ? $updatedRequest : $request);

        $this->environment->updateBlueprint('requests', $requests);

        return $this->environment;
    }
}
