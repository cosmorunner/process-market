<?php

namespace App\Environment\Commands;

use App\Environment\Bot;
use App\Models\Environment;

/**
 * Class StoreUser
 * @package App\Environment\Commands
 */
class StoreBot extends Command {

    /**
     * Erstellen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $bots = $this->environment->blueprint->bots;
        $bots->add(Bot::make($this->payload));

        $this->environment->updateBlueprint('bots', $bots);

        return $this->environment;
    }

}
