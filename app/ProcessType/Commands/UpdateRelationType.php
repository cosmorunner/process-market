<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\RelationType;

/**
 * Class UpdateRelationType
 * @package App\ProcessType\Commands
 */
class UpdateRelationType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['relation_types'];

    /**
     * Verknüpfungstyp aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->relationTypes = $this->definition->relationTypes->map(function (RelationType $relationType) {
            if ($this->payload['id'] === $relationType->id) {
                return RelationType::make($this->payload);
            }

            return $relationType;
        });

        return $this->definition;
    }

}
