<?php

namespace App\Environment\Commands;

use App\Environment\Request;
use App\Models\Environment;

/**
 * Class StoreRequest
 * @package App\Environment\Commands
 */
class StoreRequest extends Command {

    /**
     * Erstellen eines Connector-Requests.
     * @return Environment
     */
    public function command(): Environment {
        $requests = $this->environment->blueprint->requests;
        $requests->add(Request::make($this->payload));

        $this->environment->updateBlueprint('requests', $requests);

        return $this->environment;
    }
}
