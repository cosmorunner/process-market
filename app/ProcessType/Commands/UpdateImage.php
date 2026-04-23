<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateImage
 * @package App\ProcessType\Commands
 */
class UpdateImage extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['image'];

    /**
     * Rolle aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->image = $this->payload['image'] ?? 'star';

        return $this->definition;
    }

}
