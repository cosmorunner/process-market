<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class UpdateHistoryListConfig
 * @package App\ProcessType\Commands
 */
class UpdateHistoryListConfig extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['history_list_config_slug'];

    /**
     * Flag indicating whether the graph must be recalculated after the command. For example, for StoreActionType or DeleteActionRule
     * the graph must be recalculated.
     * @var bool
     */
    public $recalculate = true;

    /**
     * Rolle aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->history_list_config_slug = $this->payload['history_list_config_slug'] ?? null;

        return $this->definition;
    }

}
