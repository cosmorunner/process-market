<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\StatusRule;

/**
 * Class StoreStatusRule
 * @package App\ProcessType\Commands
 */
class StoreStatusRule extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    public $recalculate = true;

    /**
     * Statusregel anlegen
     * @return Definition
     */
    public function command(): Definition {
        $model = StatusRule::make($this->payload);
        $actionType = $this->definition->actionType($model->action_type_id);

        if ($actionType instanceof ActionType) {
            $actionType->statusRules->add($model);
            $this->statusTypeContext = $model->status_type_id;
            $this->positionModelId = $actionType->id;
        }

        return $this->definition;
    }

    /**
     * @param array $position
     * @param array $newCalculated
     * @return array
     */
    public function updateGraphPositions(array $position, array $newCalculated) {
        $newCalculated = parent::updateGraphPositions($position, $newCalculated);

        if (($position['x'] ?? null) && ($position['y'] ?? null)) {
            // Bei einer neuen "Statusrule Node" (=?, +?, -?) wird die Node ~50px von der Aktionstyp-Node entfernt hinzugefügt.
            foreach ($newCalculated as $key => $item) {
                $type = $item['data']['type'];
                $atypId = $item['data']['action_type_id'] ?? null;
                $statusTypId = $item['data']['status_type_id'] ?? null;

                // Nur Statusregel-Nodes prüfen, die noch KEINE position haben.
                if (!$atypId || !$statusTypId || $type !== 'statusrule-node' || array_key_exists('position', $item)) {
                    continue;
                }

                // Aktionstyp-Node ermitteln
                $actionTypeNode = collect($newCalculated)->first(function (array $node) use ($atypId, $statusTypId) {
                    $type = $node['data']['type'] ?? null;
                    $modelId = $node['data']['model_id'] ?? null;
                    $nodeStatusTypeId = $node['data']['status_type_id'] ?? null;

                    return $type === 'action' && $atypId === $modelId && $statusTypId === $nodeStatusTypeId && array_key_exists('position', $node);
                });

                // Falls die Aktionstyp-Node existiert, wird die Statusrule-Node dort in der Nähe platziert.
                if ($actionTypeNode) {
                    $item['position'] = [
                        'x' => $actionTypeNode['position']['x'] + rand(-50, 50),
                        'y' => $actionTypeNode['position']['y'] + rand(-50, 50)
                    ];

                    $newCalculated[$key] = $item;
                }
            }
        }

        return $newCalculated;
    }

}
