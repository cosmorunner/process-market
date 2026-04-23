<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateStatusTypes
 * @package App\ProcessType\Commands
 */
class UpdateStatusTypes extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    public $recalculate = true;

    /**
     * Statustypen aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $statusTypes = $this->payload['items'];

        foreach ($statusTypes as $statusType) {
            $this->definition = (new UpdateStatusType($statusType, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }

}
