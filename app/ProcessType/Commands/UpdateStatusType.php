<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\StatusType;

/**
 * Class UpdateStatusType
 * @package App\ProcessType\Commands
 */
class UpdateStatusType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    public $recalculate = true;

    /**
     * Statustyp aktualisieren.
     * @return Definition
     */
    public function command(): Definition {
        $statusTypeId = $this->payload['id'];

        $this->definition->statusTypes = $this->definition->statusTypes->map(function (StatusType $statusType) use ($statusTypeId) {
            if ($statusType->id === $statusTypeId) {
                return StatusType::make($this->payload);
            }

            return $statusType;
        });

        return $this->definition;
    }

}
