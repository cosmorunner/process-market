<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class StoreStatusTypeBulk
 * @package App\ProcessType\Commands
 */
class StoreStatusTypeBulk extends Command {

    /**
     * The graph must be recalculated.
     * @var bool
     */
    public $recalculate = true;

    /**
     * @return Definition
     */
    public function command(): Definition {
        foreach ($this->payload['value'] as $row){
            $parts = array_map('trim',explode(';', $row));
            $payload = [
                'default' => $parts[1] ?? '-1',
                'image' => 'settings',
                'name' => trim($parts[0]),
            ];

            $this->definition = (new StoreStatusType($payload, $this->definition, $this->processVersion))->execute();
        }
        return $this->definition;
    }
}
