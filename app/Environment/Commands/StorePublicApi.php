<?php

namespace App\Environment\Commands;

use App\Environment\PublicApi;
use App\Models\Environment;

/**
 * Class StorePublicApi
 * @package App\Environment\Commands
 */
class StorePublicApi extends Command {

    /**
     * Erstellen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $publicApis = $this->environment->blueprint->publicApis;
        $publicApis->add(PublicApi::make($this->payload));
        $this->environment->updateBlueprint('publicApis', $publicApis);

        return $this->environment;
    }

}
