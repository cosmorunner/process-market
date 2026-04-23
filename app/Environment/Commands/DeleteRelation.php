<?php

namespace App\Environment\Commands;

use App\Environment\Relation;
use App\Models\Environment;

/**
 * Class DeleteProcess
 * @package App\Environment\Commands
 */
class DeleteRelation extends Command {

    /**
     * Löschen einer Verknüpfung.
     * @return Environment
     */
    public function command(): Environment {
        $relations = $this->environment->blueprint->relations->filter(fn(Relation $relation) => $relation->id !== $this->payload['id'] ?? null);
        $this->environment->updateBlueprint('relations', $relations);

        return $this->environment;
    }
}
