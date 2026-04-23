<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\RelationType;

/**
 * Class StoreRelationType
 * @package App\ProcessType\Commands
 */
class StoreRelationType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['relation_types'];

    /**
     * Verknüpfungstyp anlegen.
     * @return Definition
     */
    public function command(): Definition {
        $relationType = RelationType::make($this->payload);
        $this->definition->relationTypes->add($relationType);

        return $this->definition;
    }

}
