<?php /** @noinspection PhpDocSignatureInspection */

namespace App\Graph;

use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\State;
use App\ProcessType\StatusRule;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class StatusNode
 * @package App\Graph
 */
class StatusNode extends Node {

    use UsesParenthood;

    /**
     * Relevanz der Node, wird für die Berechnung der CompoundNodes benötigt.
     * @var int
     */
    private int $relevance = 0;

    /**
     * Gibt die Node in im Datenformat einer Cytoscape-Node zurück.
     * @return array
     */
    public function transform(): array {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->model->name,
                'type' => 'status',
                'model_id' => $this->model->id
            ],
            'classes' => 'node status'
        ];
    }

    /**
     * Fügt alle Zustände und gruppierte Zustände (CompoundNodes) hinzu.
     * @param Collection $actionTypes
     */
    public function calculate(Collection $actionTypes) {
        // Aktionen zum Graphen hinzufügen
        $actionTypes->each(function (ActionType $actionType) {
            $this->addNode(new ActionNode($actionType, $this, $this->model->id));
        });

        // Zustände hinzufügen
        $this->model->states->each(function (State $state) {
            $stateNode = new StateNode($state, $this);
            $this->addNode($stateNode);
        });

        // Für jeden Aktionstyp für die Aktions- und Statusregel die Compound Nodes prüfen.
        // Wenn eine Regel nur einen Zustand verknüpft, diesen einzeln hinzufügen.
        $this->actionNodes()->each(function (ActionNode $actionNode) {
            $actionRule = $actionNode->model->actionRules->firstWhere('status_type_id', '=', $this->model->id);
            $statusRule = $actionNode->model->statusRules->firstWhere('status_type_id', '=', $this->model->id);

            if ($actionRule instanceof ActionRule) {
                $stateNodes = $this->stateNodesByActionRule($actionRule);
                $this->addNodeOrCompound($stateNodes);
            }
            if ($statusRule instanceof StatusRule && $statusRule->operator === StatusRule::OPERATOR_SET) {
                $stateNodes = $this->stateNodesByStatusRule($statusRule, $actionNode->model);
                $this->addNodeOrCompound($stateNodes);
            }
        });

        // Einzelne Nodes entfernen, die sich in einer CompoundNode befinden.
        $this->nodes()->each(function (Node $node) {
            $this->compoundNodes()->each(function (CompoundNode $compoundNode) use ($node) {
                $nodeIds = $compoundNode->nodes()->pluck('model')->pluck('id');
                if ($nodeIds->contains($node->model->id)) {
                    $this->removeNode($node);
                }
            });
        });

        // Nodes in CompoundNodes (Max. Tiefe 2) gruppieren/nesten.
        $this->groupNodesToCompounds();

        // Kanten hinzufügen
        $this->calculateEdges();
    }

    /**
     * Gemäß der Gruppierung von State Charts werden hier die Zustände bestmöglich in CompoundNodes gruppiert/genestet.
     * Zunächst werden die CompoundNodes nach Relevanz sortiert. Eine CompoundNode hat eine höhere Relevanz wenn
     * mehr Aktionen auf diese durch Aktions- oder Statusregeln verweisen.
     * Es wird versucht, die States niedrigerer CompoundNodes bestmöglich in höhere CompoundNodes einzugruppieren,
     * damit letzen Endes die Anzahl der Kanten reduziert werden kann.
     */
    private function groupNodesToCompounds() {
        if ($this->compoundNodesCount() <= 1) {
            return;
        }

        for ($i = 0; $i < $this->compoundNodesCount(); $i++) {
            /* @var CompoundNode $compoundNode */
            $sorted = $this->compoundNodes()->sortByDesc('relevance')->values();
            $compoundNode = $sorted->get($i);
            $stateIds = $compoundNode->nodes()->pluck('model')->pluck('id');

            for ($k = $i + 1; $k < $this->compoundNodesCount(); $k++) {
                /* @var CompoundNode $nextCompound */
                $nextCompound = $sorted->get($k);

                if (!$nextCompound instanceof CompoundNode) {
                    continue;
                }

                $nextStateIds = $nextCompound->nodes()->pluck('model')->pluck('id');

                // Sollte es im $nextCompound States geben, die bereits in der $compoundNode sind,
                // werden diese dort entfernt und in der $compoundNode hinzugefügt.
                $duplicateStateIds = $stateIds->intersect($nextStateIds);
                $sharedStateNodes = $nextCompound->nodes()->filter(function (Node $node) use ($duplicateStateIds) {
                    return $duplicateStateIds->contains($node->model->id);
                });

                // Bei > 1 neue Compound Node erstellen
                if ($sharedStateNodes->count() > 1) {
                    // Aus $compoundNode und $nextCompound entfernen
                    $nextCompound->removeNodes($sharedStateNodes);

                    if ($compoundNode->nodesCount() > $sharedStateNodes->count()) {
                        $compoundNode->removeNodes($sharedStateNodes);

                        // Nodes in $compoundNode als eigene CompoundNode erstellen
                        $newCompoundNode = new CompoundNode($this->model, $compoundNode, [], 'compound-level-two');
                        $newCompoundNode->addNodes($sharedStateNodes);
                        $newCompoundNode->increaseRelevance($sharedStateNodes->count());
                        $compoundNode->addCompoundNode($newCompoundNode);
                    }
                }

                // Bei genau einem Zustand, diesen entfernen
                if ($sharedStateNodes->count() === 1) {
                    $nextCompound->removeNode($sharedStateNodes->first());
                }
            }
        }

        // CompoundNodes mit nur einem Zustand auflösen, leere CompoundNodes entfernen
        $this->compoundNodes()->each(function (CompoundNode $compoundNode) {
            // Wenn $nextCompound nur noch eine Node hat, die CompoundNode auflösen.
            if ($compoundNode->nodesCount() === 1 && $compoundNode->compoundNodesCount() === 0) {
                $first = $compoundNode->nodes()->first();

                if ($this->nodeByStateId($first->model->id) === null) {
                    $this->addNode($first);
                    $this->removeCompoundNode($compoundNode);
                }
            }
            // Wenn $nextCompound keine Nodes mehr hat, diese CompoundNode entfernen.
            if ($compoundNode->nodesCount() === 0) {
                $this->removeCompoundNode($compoundNode);
            }
        });
    }

    /**
     * Fügt die Kanten hinzu.
     */
    private function calculateEdges() {
        $this->actionNodes()->each(function (ActionNode $actionNode) {
            // Zustand/CompoundNode -> Action Edge
            $actionNode->model->actionRules->each(function (ActionRule $actionRule) use ($actionNode) {
                if ($actionRule->status_type_id !== $this->model->id) {
                    return;
                }

                $actionEdges = collect();

                // Zunächst an alle Zustände verknüpfen.
                $stateNodes = $this->stateNodesByActionRule($actionRule, true);

                $stateNodes->each(function (StateNode $stateNode) use ($actionNode, $actionEdges, $actionRule) {
                    $actionEdges->add($this->addEdge(new Edge($stateNode, $actionNode, 'action-rule-edge', $actionRule->id)));
                });

                // 1. CompoundEbene.
                $this->compoundNodes()
                    ->each(function (CompoundNode $compoundNode) use ($stateNodes, $actionNode, $actionEdges, $actionRule) {
                        $stateNodeIds = $compoundNode->stateNodes(true)->pluck('id');
                        $sourceIdsInThisCompound = $actionEdges->filter(function (Edge $edge) use ($stateNodeIds) {
                            return $stateNodeIds->contains($edge->source->id);
                        })->pluck('source')->pluck('id');

                        // Wenn die CompoundNode alle States hat, die mit der Aktion verknüpft werden sollen.
                        $shared = $stateNodeIds->intersect($sourceIdsInThisCompound);
                        if ($shared->count() === $stateNodeIds->count()) {
                            $this->removeEdges($actionEdges->filter(function (Edge $edge) use ($stateNodeIds) {
                                return $stateNodeIds->contains($edge->source->id);
                            }));
                            $this->addEdge(new Edge($compoundNode, $actionNode, 'action-rule-edge', $actionRule->id));
                        }
                        else {
                            // 2. CompoundEbene.
                            $compoundNode->compoundNodes()
                                ->each(function (CompoundNode $compoundNode) use ($stateNodes, $actionNode, $actionEdges, $actionRule) {
                                    $stateNodeIds = $compoundNode->stateNodes(true)->pluck('id');
                                    $actionEdgesInThisCompound = $actionEdges->filter(function (Edge $edge) use ($stateNodeIds) {
                                        return $stateNodeIds->contains($edge->source->id);
                                    })->pluck('source')->pluck('id');
                                    // Wenn die CompoundNode alle States hat, die mit der Aktion verknüpft werden sollen.
                                    $shared = $stateNodeIds->intersect($actionEdgesInThisCompound);
                                    if ($shared->count() === $stateNodeIds->count()) {
                                        $this->removeEdges($actionEdges->filter(function (Edge $edge) use ($stateNodeIds) {
                                            return $stateNodeIds->contains($edge->source->id);
                                        }));
                                        $this->addEdge(new Edge($compoundNode, $actionNode, 'action-rule-edge', $actionRule->id));
                                    }
                                });
                        }
                    });
            });

            // Action -> Zustand/CompoundNode Edges
            // Zunächst nur die SET-Statusregeln
            $statusRules = $actionNode->model->statusRules->filter(function (StatusRule $statusRule) {
                return $statusRule->status_type_id === $this->model->id && $statusRule->operator === StatusRule::OPERATOR_SET;
            });

            $statusRules->each(function (StatusRule $statusRule) use ($actionNode) {
                $actionEdges = collect();
                $stateNodes = $this->stateNodesByStatusRule($statusRule, $actionNode->model, true);

                // Falls bei der SET-Statusregel keine Node ermittelt wurde, wird eine "Fragezeichen"-Node hinzugefügt.
                if ($stateNodes->isEmpty()) {
                    $statusRuleNode = new StatusRuleNode($statusRule, $this);
                    $this->addNode($statusRuleNode);
                    $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge', $statusRule->id);
                    $this->addEdge($edge);
                }
                else {
                    // Zustands-Nodes ermitteln und dann die Verknüpfungen machen.
                    $stateNodes->each(function (StateNode $stateNode) use ($actionNode, $actionEdges, $statusRule) {
                        $actionEdges->add($this->addEdge(new Edge($actionNode, $stateNode, 'status-rule-edge', $statusRule->id)));
                    });
                }

                // 1. CompoundEbene.
                $this->compoundNodes()
                    ->each(function (CompoundNode $compoundNode) use ($stateNodes, $actionNode, $actionEdges, $statusRule) {
                        $stateNodeIds = $compoundNode->stateNodes(true)->pluck('id');

                        $targetIdsInThisCompound = $actionEdges->filter(fn(Edge $edge) => $stateNodeIds->contains($edge->target->id))
                            ->pluck('target')
                            ->pluck('id');

                        // Wenn die CompoundNode alle States hat, die mit der Aktion verknüpft werden sollen.
                        $shared = $stateNodeIds->intersect($targetIdsInThisCompound);

                        if ($shared->count() === $stateNodeIds->count()) {
                            $this->removeEdges($actionEdges->filter(fn(Edge $edge) => $stateNodeIds->contains($edge->target->id)));
                            $this->addEdge(new Edge($actionNode, $compoundNode, 'status-rule-edge', $statusRule->id));
                        }
                        else {
                            // 2. CompoundEbene.
                            $compoundNode->compoundNodes()
                                ->each(function (CompoundNode $compoundNode) use ($stateNodes, $actionNode, $actionEdges, $statusRule) {
                                    $stateNodeIds = $compoundNode->stateNodes(true)->pluck('id');

                                    $actionEdgesInThisCompound = $actionEdges->filter(fn(Edge $edge) => $stateNodeIds->contains($edge->target->id))
                                        ->pluck('target')
                                        ->pluck('id');

                                    // Wenn die CompoundNode alle States hat, die mit der Aktion verknüpft werden sollen.
                                    $shared = $stateNodeIds->intersect($actionEdgesInThisCompound);

                                    if ($shared->count() === $stateNodeIds->count()) {
                                        $this->removeEdges($actionEdges->filter(fn(Edge $edge) => $stateNodeIds->contains($edge->target->id)));
                                        $this->addEdge(new Edge($actionNode, $compoundNode, 'status-rule-edge', $statusRule->id));
                                    }
                                });
                        }
                    });
            });

            // Dann für die ADD/SUB Statusregeln
            $statusRules = $actionNode->model->statusRules->filter(function (StatusRule $statusRule) {
                return $statusRule->status_type_id === $this->model->id && in_array($statusRule->operator, [
                        StatusRule::OPERATOR_ADD,
                        StatusRule::OPERATOR_SUB
                    ]);
            });

            // Extra Node hinzfügen für Add/Sub mit einem Fragezeichen, z.B. +? oder -?
            $statusRules->each(function (StatusRule $statusRule) use ($actionNode) {
                $statusRuleNode = new StatusRuleNode($statusRule, $this);
                $this->addNode($statusRuleNode);
                $edge = new Edge($actionNode, $statusRuleNode, 'status-rule-edge', $statusRule->id);
                $this->addEdge($edge);
            });

        });
    }

    /**
     * Je nach Anzahl der States wird entweder ein einzelner State oder eine Compound-Node hinzugefügt.
     * @param Collection $stateNodes
     */
    private function addNodeOrCompound(Collection $stateNodes) {
        if ($stateNodes->count() > 1 && $stateNodes->count()) {
            $nodes = $stateNodes->map(function (StateNode $stateNode) {
                return new StateNode($stateNode->model);
            });

            // Nur wenn es noch keine CompoundNode mit diesen Zuständen gibt.
            $compoundNode = $this->compoundNodeByNodes($nodes);

            if (is_null($compoundNode)) {
                $compoundNode = new CompoundNode($this->model, null, [], 'compound-level-one');
                $compoundNode->addNodes($nodes);
                $compoundNode->increaseRelevance($nodes->count());
                $this->addCompoundNode($compoundNode);
            } // Ansonsten die Relevanz erhöhen.
            else {
                $compoundNode->increaseRelevance($nodes->count());
            }
        }

        if ($stateNodes->count() === 1) {
            // Nur wenn noch nicht vorhanden
            if ($this->nodeByStateId($stateNodes->first()->model->id) === null) {
                $this->addNode(new StateNode($stateNodes->first()->model, $this));
                $this->increaseRelevance(1);
            }
        }
    }

    /**
     * Gibt die Statenode mit dem Initialwert des Status zurück.
     * @return StateNode|null
     */
    public function getInitialStateNode() {
        return $this->stateNodeByValue($this->model->default, true);
    }

    /**
     * Gibt den Zustand zurück, welcher den Wert einnimmt.
     * @param string $value
     * @param bool $includeNestedStates
     * @return StateNode|null
     */
    public function stateNodeByValue(string $value, $includeNestedStates = false) {
        return $this->stateNodes($includeNestedStates)->first(function (StateNode $state) use ($value) {
            return ActionRule::operatorInBetween($value, $state->model->min, $state->model->max);
        });
    }

    /**
     * Gibt die Zustände zurück, welche durch eine Statusregel angesprochen werden können.
     * Kann ausschließlich für den SET-Operator genutzt werden.
     * @param StatusRule $rule
     * @param ActionType $actionType
     * @param bool $includeNestedStates
     * @return Collection
     */
    public function stateNodesByStatusRule(StatusRule $rule, ActionType $actionType, $includeNestedStates = false): Collection {
        if ($rule->operator !== StatusRule::OPERATOR_SET) {
            return collect();
        }

        $values = $rule->values;

        // Dynamische Statusregel --> mögliche Werte aus Feld ermitteln
        if ($rule->output) {
            // Aus [[action.outputs.field_1]] --> field_1 ermitteln
            $values = $actionType->getPossibleValues(SyntaxParser::actionOutputName($rule->output));
        }

        // Statusregel zeigt auf einen Zustand.
        if ($rule->state) {
            // Zustand mittels Zustand-Id ermitteln
            if (($state = $this->model->state($rule->state)) instanceof State) {
                $values = [$state->min];
            }
        }

        // Conditions
        if (!empty($rule->conditions) && !$rule->output) {
            foreach ($rule->conditions as $condition) {
                // Falls ein Zustand bei der Kondition gewählt wurde
                if (Uuid::isValid($condition[0]) && ($state = $this->model->state($condition[0]))) {
                    $values[] = $state->min;
                }
                else {
                    $values[] = $condition[0];
                }
            }
        }

        $values = array_unique($values);

        return $this->stateNodesByValues($values, $includeNestedStates);
    }

    /**
     * Gibt die Zustände zurück, auf denen sich eine Aktionsregel bezieht.
     * @param ActionRule $rule
     * @param $includeNestedStates
     * @return Collection|StateNode[]
     */
    public function stateNodesByActionRule(ActionRule $rule, $includeNestedStates = false): Collection {
        $stateNodes = collect();

        switch ($rule->operator) {
            case ActionRule::OPERATOR_LOWER:
                if ($rule->usesValues()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorLower($node->model->max, $rule->values[0]));
                }
                if ($rule->usesStates()) {
                    $stateNode = $this->stateNodes($includeNestedStates)
                        ->first(fn(StateNode $node) => $node->model->id === $rule->state_ids[0]);

                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorLower($node->model->max, $stateNode->model->min));
                }
                break;
            case ActionRule::OPERATOR_LOWER_OR_EQUAL:
                if ($rule->usesValues()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorLowerOrEqual($node->model->min, $rule->values[0]));
                }
                if ($rule->usesStates()) {
                    $stateNode = $this->stateNodes($includeNestedStates)
                        ->first(fn(StateNode $node) => $node->model->id === $rule->state_ids[0]);

                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorLowerOrEqual($node->model->max, $stateNode->model->max));
                }
                break;
            case ActionRule::OPERATOR_GREATER_OR_EQUAL:
                if ($rule->usesValues()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorGreaterOrEqual($node->model->min, $rule->values[0]));
                }
                if ($rule->usesStates()) {
                    $stateNode = $this->stateNodes($includeNestedStates)
                        ->first(fn(StateNode $node) => $node->model->id === $rule->state_ids[0]);
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorGreaterOrEqual($node->model->min, $stateNode->model->min));
                }
                break;
            case ActionRule::OPERATOR_GREATER:
                if ($rule->usesValues()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorGreater($node->model->min, $rule->values[0]));
                }
                if ($rule->usesStates()) {
                    $stateNode = $this->stateNodes($includeNestedStates)
                        ->first(fn(StateNode $node) => $node->model->id === $rule->state_ids[0]);
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => ActionRule::operatorGreater($node->model->min, $stateNode->model->max));
                }
                break;
            case ActionRule::OPERATOR_IN_ARRAY:
                if ($rule->usesValues()) {
                    $stateNodes = $this->stateNodesByValues($rule->values, $includeNestedStates);
                }
                if ($rule->usesStates()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => in_array($node->model->id, $rule->state_ids));
                }
                break;
            case ActionRule::OPERATOR_NOT_IN_ARRAY:
                if ($rule->usesValues()) {
                    // Alle Zustände abzüglich jenen wo der Min UND Max Wert dem Regelwert entspricht.
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(function (StateNode $node) use ($rule) {
                            if ($node->model->min === $node->model->max && in_array($node->model->min, $rule->values)) {
                                return false;
                            }

                            return true;
                        });
                }
                if ($rule->usesStates()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => !in_array($node->model->id, $rule->state_ids));
                }
                break;
            case ActionRule::OPERATOR_IN_BETWEEN:
                if ($rule->usesValues()) {
                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(function (StateNode $node) use ($rule) {
                            return ActionRule::operatorGreaterOrEqual($node->model->min, $rule->values[0]) && ActionRule::operatorLowerOrEqual($node->model->max, $rule->values[1]);
                        });
                }
                if ($rule->usesStates()) {
                    $inBetweenStateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(fn(StateNode $node) => in_array($node->model->id, $rule->state_ids));

                    $values = [];
                    $inBetweenStateNodes->each(function (StateNode $node) use (&$values) {
                        $values = [...$values, $node->model->min, $node->model->max];
                    });

                    $values = ActionRule::sortAscending($values);
                    $min = $values[0];
                    $max = $values[count($values) - 1];

                    $stateNodes = $this->stateNodes($includeNestedStates)
                        ->filter(function (StateNode $node) use ($min, $max) {
                            return ActionRule::operatorGreaterOrEqual($node->model->min, $min) && ActionRule::operatorLowerOrEqual($node->model->max, $max);
                        });
                }
                break;
        }

        return $stateNodes;
    }

    /**
     * Gibt Zustände anhand von Werten zurück.
     * Es wird für jeden Wert geprüft ob ein Zustandbereich den Wert abdeckt.
     * @param array $values
     * @param $includeNestedStates
     * @return Collection
     */
    public function stateNodesByValues(array $values, $includeNestedStates = false) {
        $states = collect();

        foreach ($values as $value) {
            $stateNode = $this->stateNodeByValue($value, $includeNestedStates);

            if ($stateNode instanceof StateNode) {
                $states->push($stateNode);
            }
        }

        return $states;
    }

    /**
     * Sucht eine Compound-Node anhand von Zuständen.
     * @param Collection $stateNodes
     * @return CompoundNode
     */
    public function compoundNodeByNodes(Collection $stateNodes) {
        foreach ($this->compoundNodes() as $compoundNode) {
            $stateIds = $compoundNode->nodes()->pluck('model')->pluck('id')->toArray();
            $idsToCheck = $stateNodes->pluck('model')->pluck('id')->toArray();

            if (count($stateIds) === count($idsToCheck) && count(array_intersect($stateIds, $idsToCheck)) === count($stateIds)) {
                return $compoundNode;
            }
        }

        return null;
    }
}
