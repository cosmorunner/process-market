<?php

namespace App\Environment\Commands;

use App\Environment\Relation;
use App\Models\Environment;

/**
 * Class StoreRelation
 * @package App\Environment\Commands
 */
class StoreRelation extends Command {

    /**
     * Erstellen einer Verknüpfung.
     * @return Environment
     */
    public function command(): Environment {
        $relations = $this->environment->blueprint->relations;
        $relations->add(Relation::make($this->payload));
        $this->environment->updateBlueprint('relations', $relations);

        return $this->environment;
    }
}
