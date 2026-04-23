<?php

namespace App\Environment\Commands;

use App\Environment\Bot;
use App\Models\Environment;

/**
 * Class DeleteBot
 * @package App\Environment\Commands
 */
class DeleteBot extends Command {

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $bots = $this->environment->blueprint->bots->filter(fn(Bot $bot) => $bot->id !== $this->payload['id'] ?? null);
        $this->environment->updateBlueprint('bots', $bots);

        return $this->environment;
    }

}
