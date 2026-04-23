<?php /** @noinspection PhpUnused */

namespace App\Graph;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Class Cytoscape
 * @package App\Graph
 */
class Cytoscape implements Transformable {

    use UsesParenthood;

    /**
     * Definition des Prozesstyps.
     * @var Definition
     */
    public Definition $definition;

    /**
     * Reports
     * @var array
     */
    private $messages = [];

    public function __construct(Definition $definition) {
        $this->definition = $definition;
    }

    /**
     * @return void
     */
    public function calculate() {
        // Reset
        $this->nodes = collect();
        $this->edges = collect();

        // Statustypen zum Graphen hinzufügen
        $this->definition->statusTypes->each(function (StatusType $statusType) {

            // Aktionen, die eine Aktionsregel oder Statusregel für den Status haben hinzufügen und somit
            // innerhalb der Status-Node liegen.
            $actionTypesWithinStatus = $this->definition->actionTypes->filter(function (ActionType $actionType) use ($statusType) {
                return $actionType->actionRules->pluck('status_type_id')
                        ->contains($statusType->id) || $actionType->statusRules->pluck('status_type_id')
                        ->contains($statusType->id);
            });

            $statusNode = $this->addNode(new StatusNode($statusType));
            $statusNode->calculate($actionTypesWithinStatus);
        });

        // Aktionen ohne Regeln hinzufügen.
        $liberalActions = $this->definition->actionTypes->filter(function (ActionType $actionType) {
            return $actionType->actionRules->isEmpty() && $actionType->statusRules->isEmpty();
        });

        $liberalActions->each(function (ActionType $actionType) {
            $this->addNode((new LiberalActionNode($actionType)));
        });

        // CSS-Klassen für Zustandsnodes in den Compound-Nodes setzen.
        $this->statusNodes()->each(function (StatusNode $statusNode) {
            // CSS-Klassen
            $statusNode->stateNodes(true)->each(function (StateNode $stateNode) {
                if (str_contains($stateNode->parent->classes, 'compound-level-one')) {
                    $stateNode->addCustomClasses('state-level-one');
                }
                if (str_contains($stateNode->parent->classes, 'compound-level-two')) {
                    $stateNode->addCustomClasses('state-level-two');
                }
            });
        });
    }

    /**
     * Berechnet den Graphen und gibt die Cy-Elemente zurück.
     * @return array
     */
    public function transform(): array {
        // Nodes und Kanten berechnen
        $this->calculate();

        $nodes = collect();
        $edges = collect();

        // Alle Status und ihre Sub-Graphen hinzufügen
        $this->statusNodes()->each(function (StatusNode $statusNode) use (&$nodes, &$edges) {
            $nodes->add($statusNode->transform());

            $statusNode->nodes()->each(function (Node $node) use (&$nodes) {
                $nodes->add($node->transform());
            });

            // Alle Kanten von freistehenden Nodes zu Zuständen in Status-Subgraphen hinzufügen.
            $statusNode->edges()->each(function (Edge $edge) use (&$edges) {
                $edges->add($edge->transform());
            });

            $statusNode->compoundNodes()->each(function (CompoundNode $compoundNode) use (&$nodes) {
                $nodes->add($compoundNode->transform());

                $compoundNode->compoundNodes()->each(function (CompoundNode $compoundNode) use (&$nodes) {
                    $nodes->add($compoundNode->transform());
                    $compoundNode->nodes()->each(function (Node $node) use (&$nodes) {
                        $nodes->add($node->transform());
                    });
                });

                $compoundNode->nodes()->each(function (Node $node) use (&$nodes) {
                    $nodes->add($node->transform());
                });
            });
        });

        // Alle Kanten von freistehenden Nodes zu Zuständen in Status-Subgraphen hinzufügen.
        $this->edges()->each(function (Edge $edge) use (&$edges) {
            $edges->add($edge->transform());
        });

        $this->liberalActionNodes()->each(function (LiberalActionNode $node) use (&$edges) {
            $edges->add($node->transform());
        });

        return [...$nodes->toArray(), ...$edges->toArray()];
    }

    /**
     * Gibt alle ActionNode-Children zurück.
     * @return Collection
     */
    private function statusNodes() {
        return $this->nodes->filter(function ($ele) {
            return $ele instanceof StatusNode;
        });
    }

    /**
     * Gibt die Node zurück welche die liberale Aktionen hält.
     * @return Collection
     */
    private function liberalActionNodes() {
        return $this->nodes->filter(function ($ele) {
            return $ele instanceof LiberalActionNode;
        });
    }

    /**
     * Versucht bestmöglich aus einem vorherigen Array an CY-Elementen die Positionen für die neuen Elemente zu setzen.
     * @param array $oldCalculated
     * @param array $newCalculated
     * @return array
     */
    public static function applyOldPositions(array $newCalculated, array $oldCalculated) {
        foreach ($newCalculated as $key => $item) {
            $data = $item['data'];
            $type = $data['type'] ?? null;
            $modelId = $data['model_id'] ?? null;
            $statusTypeId = $data['status_type_id'] ?? null;
            $existing = self::queryNode($oldCalculated, $type, $modelId, $statusTypeId);

            // Positionen für StatusTyp, State, Action in Statustyp, Start und Statusregel
            // und Liberal Action-Nodes von vorheriger Berechnung übernehmen
            if ($existing && array_key_exists('position', $existing)) {
                $item['position'] = $existing['position'];
            }

            $newCalculated[$key] = $item;
        }

        return $newCalculated;
    }

    /**
     * Applies old html classes to the new elements.
     * @param array $oldCalculated
     * @param array $newCalculated
     * @return array
     */
    public static function applyOldClasses(array $newCalculated, array $oldCalculated) {
        foreach ($newCalculated as $key => $item) {
            $data = $item['data'];
            $type = $data['type'] ?? null;
            $modelId = $data['model_id'] ?? null;
            $statusTypeId = $data['status_type_id'] ?? null;
            $existing = self::queryNode($oldCalculated, $type, $modelId, $statusTypeId);

            // Classes for status type, state, action in status type, start and status rule
            // and Liberal Action nodes from previous calculation
            if ($existing && array_key_exists('classes', $existing) && !empty($existing['classes'])) {
                $item['classes'] = $existing['classes'];
            }

            $newCalculated[$key] = $item;
        }

        return $newCalculated;
    }

    /**
     * Sets empty position with positions on the viewport.
     * Multiple elements will be arranged in a square.
     * Uses a fixed size of 64, 64 as dinstance between elements in the square.
     * @param array $calculated
     * @param array $viewPort
     * @return array
     **/
    public static function setEmptyPositions(array $calculated, array $viewPort) {
        if (!array_key_exists('x', $viewPort) || !array_key_exists('y', $viewPort)) {
            return $calculated;
        }

        // Size of a single object. May need a proper static value somewhere!
        $single = ['x' => 232, 'y' => 112];
        // The viewPort is the vector to 0, 0. Therefor the negative of viewPort points to the top left of the viewable screen
        $topLeft = ['x' => -$viewPort['x'], 'y' => -$viewPort['y']];

        // apply offset
        $topLeft['x'] += $single['x'] / 2;
        $topLeft['y'] += $single['y'] / 2;

        $toPlace = [];

        foreach ($calculated as $key => $item) {
            if (str_contains($item['classes'], 'node') && !array_key_exists('position', $item)) {
                $toPlace[] = $key;
            }
        }

        $count = count($toPlace);

        if ($count === 0) {
            return $calculated;
        }

        // Determines the width (and height) of the smallest square that fits all nodes
        $width = ceil(sqrt($count));

        $placed = 0;
        foreach ($toPlace as $item) {

            $calculated[$item]['position'] = [
                'x' => $topLeft['x'] + ($placed % $width) * $single['x'],
                'y' => $topLeft['y'] + floor($placed / $width) * $single['y']
            ];
            $placed++;
        }

        return $calculated;
    }

    /**
     * @param array $oldCalculated
     * @param string $type
     * @param string|null $modelId
     * @param string|null $statusTypeId
     * @return mixed|null
     */
    private static function queryNode(array $oldCalculated, string $type, string $modelId = null, string $statusTypeId = null) {
        foreach ($oldCalculated as $item) {
            if ($modelId && $statusTypeId) {
                if ($item['data']['type'] === $type && ($item['data']['model_id'] ?? null) === $modelId && array_key_exists('status_type_id', $item['data']) && $item['data']['status_type_id'] === $statusTypeId) {
                    return $item;
                }
            }
            else if ($modelId) {
                if ($item['data']['type'] === $type && $item['data']['model_id'] === $modelId) {
                    return $item;
                }
            }
            else if ($item['data']['type'] === $type) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Renders the appropriate HTML element depending on which type is passed
     * @param Definition $definition
     * @param array $calculated
     * @return array
     * @throws Throwable
     */
    public static function renderHTMLElements(Definition $definition, array $calculated): array {
        foreach ($calculated as $key => $item) {
            $type = 'state';
            $isEnd = Cytoscape::isEndNode($item, $calculated);

            if ($isEnd) {
                $type = 'end';
            }

            // initial should have higher "weight" than end to make sure that the users knows that the status starts where it is suppose to end
            if (Cytoscape::isInitialNode($item, $definition, $calculated)) {
                $type = 'initial';
            }

            $actionType = in_array($item['data']['type'], [
                'action',
                'liberal-action'
            ]) ? $definition->actionType($item['data']['model_id']) : null;

            $state = $item['data']['type'] === 'state' ? $definition->state($item['data']['status_type_id'], $item['data']['model_id']) : null;

            $category = $actionType ? $definition->categories->firstWhere('id', '=', $actionType->category_id) : null;

            if ($actionType || $state) {
                $viewName = match ($item['data']['type']) {
                    'liberal-action', 'action' => 'cytoscape.' . $item['data']['type'],
                    'state' => 'cytoscape.state',
                };

                $viewData = match ($item['data']['type']) {
                    'liberal-action', 'action' => [
                        'actionType' => $actionType,
                        'category' => $category,
                        'roles' => $actionType->executableByRoles(),
                    ],
                    'state' => [
                        'state' => $state,
                        'type' => $type,
                        'isEnd' => $isEnd
                    ],
                    default => [],
                };

                if ($viewName) {
                    $domContent = view($viewName, $viewData)->render();
                    if ($domContent !== null) {
                        $calculated[$key]['data']['dom'] = $domContent;
                    }
                }
            }
        }

        return $calculated;
    }

    /**
     * Checks if a given node is an end node
     * @param mixed $item
     * @param mixed $calculated
     * @return bool|void
     */
    private static function isEndNode(mixed $item, mixed $calculated) {
        if ($item['data']['type'] === 'state') {
            $isTargetInStatusRuleEdge = false;

            foreach ($calculated as $edge) {

                $edgeData = $edge['data'];

                // Check whether the state is a target in a status-rule-edge
                if ($edgeData['type'] === 'status-rule-edge' && $edgeData['target'] == $item['data']['id']) {
                    $isTargetInStatusRuleEdge = true;
                }
                // Check whether the state is the source in an action-rule-edge
                if ($edgeData['type'] === 'action-rule-edge' && $edgeData['source'] == $item['data']['id']) {
                    // If this is the case, the node cannot be an end node
                    return false;
                }

                if ($item['data']['parent'] === $edgeData['id'] && $edgeData['type'] === 'compound') {
                    return self::isEndNode($edge, $calculated);
                }
            }

            // Determine if this state is an endpoint
            return $isTargetInStatusRuleEdge;
        }
    }

    /**
     * Checks if the given item is a initial state node
     * @param mixed $item
     * @param Definition $definition
     * @param mixed $calculated
     * @return bool|void|null
     */
    private static function isInitialNode(mixed $item, Definition $definition, mixed $calculated) {
        if ($item['data']['type'] === 'state') {

            foreach ($calculated as $compareItem) {

                if ($compareItem['data']['type'] === 'status') {

                    $status = $definition->statusType($compareItem['data']['model_id']);
                    $state = $definition->state($status->id, $item['data']['model_id']);

                    if (!$state) {
                        continue;
                    }

                    return $state->max >= $status->default && $state->min <= $status->default;
                }
            }

            return false;
        }
    }
}
