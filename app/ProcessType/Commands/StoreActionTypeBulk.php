<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;

/**
 * Class StoreActionTypeBulk
 * @package App\ProcessType\Commands
 */
class StoreActionTypeBulk extends Command {

    /**
     * The graph must be recalculated.
     * @var bool
     */
    public $recalculate = true;

    /**
     * @return Definition
     */
    public function command(): Definition {
        $workflowId = $this->definition->categories->where('name', '=', 'Workflow')->first()->id;

        foreach ($this->payload['value'] as $value) {
            $payload = [
                'category_id' => $workflowId,
                'full_width' => false,
                'image' => "flash_on",
                'instant' => false,
                'name' => trim($value),
                'save_label' => "app.execute",
                'show_save_button' => true,
            ];

            $this->definition = (new StoreActionType($payload, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }
}