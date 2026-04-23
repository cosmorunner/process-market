<?php

namespace App\ProcessType\Commands;

use App\Graph\Graph;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use Database\Builder\Definition\StateBuilder;
use Illuminate\Support\Str;

/**
 * Class StoreStatus
 * @package App\ProcessType\Commands
 */
class StoreStatusType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['status_types'];

    public $recalculate = true;

    public string $createdStatusTypeId = '';

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $sort = $this->definition->statusTypes->max('sort') + 1;
        $this->payload['sort'] = $sort;

        // Auto reference
        if (empty($this->payload['reference'])) {
            $reference = Str::limit(Str::slug($this->payload['name'] ?? 'Neuer Status', '_'), 16, '');

            if ($this->definition->statusType($reference) instanceof StatusType) {
                $reference .= '_' . strtolower(Str::random(4));
            }

            $this->payload['reference'] = $reference;
        }

        $model = StatusType::make($this->payload);
        $this->createdStatusTypeId = $model->id;
        $this->positionModelId = $model->id;
        $this->definition->statusTypes->add($model);

        return $this->definition;
    }

    /**
     * @param Definition $updatedDefinition
     * @return array
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $newPayload = app(StateBuilder::class)->defaultInitial($this->payload['default'], $this->createdStatusTypeId)->make()->toArray();
        $commands[] = new StoreState($newPayload, $this->definition, $this->processVersion);

        return $commands;
    }

    /**
     * Beim Anlegen eines Status über den "Hinzufügen"-Button im Panel wird die neue Statustyp-Node in der Nähe der
     * "Start"-Node positioniert.
     * @return array
     */
    public function preferredGraphPosition(): array {
        $graph = new Graph($this->processVersion->calculated);

        // If no liberal actions exists, we simple choose the default center position.
        if ($graph->statusNodes()->isNotEmpty()) {
            return $graph->centerPosition($graph->statusNodes());
        }
        else if ($graph->statusNodes()->isEmpty() && $graph->liberalActionNodes()->isNotEmpty()) {
            $position = $graph->centerPosition($graph->liberalActionNodes());

            return [
                'x' => $position['x'] + 200,
                'y' => $position['y']
            ];
        }
        else if ($graph->statusNodes()->count()) {
            $position = $graph->centerPosition($graph->statusNodes());

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
