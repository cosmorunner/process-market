<?php

namespace App\Environment\Commands;

use App\Environment\PublicApi;
use App\Models\Environment;

/**
 * Class DeletePublicApi
 * @package App\Environment\Commands
 */
class DeletePublicApi extends Command {

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $publicApis = $this->environment->blueprint->publicApis->filter(fn(PublicApi $publicApi) => $publicApi->id !== $this->payload['id'] ?? null);
        $this->environment->updateBlueprint('publicApis', $publicApis);

        return $this->environment;
    }
}
