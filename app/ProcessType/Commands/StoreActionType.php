<?php

namespace App\ProcessType\Commands;

use App\Graph\Graph;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use Illuminate\Support\Str;

/**
 * Class StoreAction
 * @package App\ProcessType\Commands
 */
class StoreActionType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        // Use id of "Workflow" category when no category is provided.
        $workflowId = $this->definition->categories->where('locked', '=', true)
            ->where('hidden', '=', false)
            ->first()?->id ?? Str::uuid()->toString();

        $categoryId = $this->payload['category_id'] ?? $workflowId;
        $categoryActionTypes = $this->definition->actionTypes->where('category_id', '=', $categoryId);

        // Set sort
        $this->payload['sort'] = $categoryActionTypes->max('sort') + 1;

        // Auto reference
        if (empty($this->payload['reference'])) {
            $reference = Str::limit(Str::slug($this->payload['name'] ?? 'Neue Aktion', '_'), 16, '');

            if ($this->definition->actionType($reference) instanceof ActionType) {
                $reference .= '_' . strtolower(Str::random(4));
            }

            $this->payload['reference'] = $reference;
        }

        $model = ActionType::make($this->payload);
        $model->definition = $this->definition;
        $this->positionModelId = $model->id;
        $this->definition->actionTypes->add($model);

        return $this->definition;
    }

    /**
     * Beim Anlegen einer Aktion über den "Hinzufügen"-Button im Panel wird die neue Action-Node in der Nähe der
     * "Freie Aktionen"-Node positioniert.
     * @return array
     */
    public function preferredGraphPosition(): array {
        $graph = new Graph($this->processVersion->calculated);

        // If no liberal actions exists, we simple choose the default center position.
        if ($graph->liberalActionNodes()->isNotEmpty()) {
            return $graph->centerPosition($graph->liberalActionNodes());
        }
        else if ($graph->liberalActionNodes()->isEmpty() && $graph->statusNodes()->isNotEmpty()) {
            $position = $graph->centerPosition($graph->statusNodes());

            return [
                'x' => $position['x'] + 200,
                'y' => $position['y']
            ];
        }
        else if ($graph->liberalActionNodes()->count()) {
            $position = $graph->centerPosition($graph->liberalActionNodes());

            return [
                'x' => $position['x'] + 300,
                'y' => $position['y']
            ];
        }
        else {
            return Graph::DEFAULT_CENTER_POSITION;
        }
    }
}
