<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Exception;

/**
 * Class StoreState
 * @package App\ProcessType\Commands
 */
class StoreState extends Command {

    /**
     * Definition array keys that are updated by the command.
     * Only every key is returned after the command, for improved performance.
     * If empty, everything is returned.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    public $recalculate = true;

    /**
     * Create status.
     * @return Definition
     * @throws Exception
     */
    public function command(): Definition {
        $statusType = $this->definition->statusType($this->payload['status_type_id']);
        $payload = $this->payload;

        if (!isset($this->payload['min']) || !$this->payload['min']) {
            $payload = array_merge($payload, State::nextMinMax($statusType));
        }
        // min is set, but max is missing. Max is set to min.
        else if (!isset($this->payload['max']) || !$this->payload['max']) {
            $payload = array_merge($payload, ['max' => $this->payload['min']]);
        }

        $model = State::make($payload);

        $this->positionModelId = $model->id;

        if ($statusType instanceof StatusType) {
            $statusType->states->add($model);
        }

        return $this->definition;
    }

    /**
     * When a state is created using the “Add” button in the panel, the new state node is created in the state.
     * @return array
     */
    public function preferredGraphPosition(): array {
        // "StatusTyp"-Node-Position ermitteln.
        $statusTypeId = $this->payload['status_type_id'];
        $rawNodes = collect($this->processVersion->calculated);
        $statusTypeNode = $rawNodes->firstWhere('data.model_id', '=', $statusTypeId);
        $stateNodes = $rawNodes->where('data.type', '=', 'state')->where('data.status_type_id', '=', $statusTypeId);

        if ($statusTypeNode) {
            $statusTypePosition = $statusTypeNode['position'] ?? [];

            // For existing states in the status type, the position is shifted slightly
            // so that the states are not on top of each other.
            if ($stateNodes->isNotEmpty() && !empty($statusTypePosition)) {
                return [
                    'x' => $statusTypePosition['x'] + rand(-50, 50),
                    'y' => $statusTypePosition['y'] + rand(-50, 50)
                ];
            }

            return $statusTypeNode['position'] ?? [];
        }

        return [];
    }
}
