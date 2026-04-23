<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\ListConfig;
use Ramsey\Uuid\Uuid;

/**
 * Class StoreListConfig
 * @package App\ProcessType\Commands
 */
class StoreListConfig extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['list_configs'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $config = config('list_templates')[$this->payload['template']];

        $listConfig = new ListConfig(array_merge($config, $this->payload, ['id' => Uuid::uuid4()->toString()]));
        $this->definition->listConfigs->add($listConfig);

        return $this->definition;
    }

}
