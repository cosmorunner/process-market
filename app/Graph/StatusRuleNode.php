<?php

namespace App\Graph;

use App\ProcessType\StatusRule;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Node für eine Statusregel-Node die dynamisch ist. Z.B. +10.000 bei Addition.
 * Class StatusRuleNode
 * @package App\Graph\Cytoscape
 */
class StatusRuleNode extends Node {

    /**
     * Id der Eltern-Node
     * @var string
     */
    public $parent;

    /**
     * StateNode constructor.
     * @param StatusRule $model
     * @param Node|null $parent
     */
    public function __construct(StatusRule $model, Node $parent = null) {
        parent::__construct($model);
        $this->parent = $parent;
    }

    /**
     * Gibt die Node in im Datenformat einer Cytoscape-Node zurück.
     * @return array
     */
    public function transform(): array {
        if ($this->model->operator === StatusRule::OPERATOR_SET) {
            $sign = '=';
        }
        else {
            $sign = $this->model->operator === StatusRule::OPERATOR_ADD ? '+' : '-';
        }

        $name = $sign . ' ?';

        if ($this->model->output || !empty($this->model->values)) {
            $name = $this->model->output ? $sign . ' ' . SyntaxParser::actionOutputName($this->model->output) : $sign . ($this->model->values[0] ?? 'undefined');
        }
        if (!$this->model->output && empty($this->model->values) && count($this->model->conditions)) {
            $stateIdsOrValues = collect($this->model->conditions)->map(fn($item) => $item[0])->unique();
            $values = $stateIdsOrValues->map(function ($item) {
                // If uuid, get state id and return min value
                if (Uuid::isValid($item)) {
                    $state = $this->parent->model->state($item);

                    if ($state) {
                        return str_ends_with($state->min, '.000') ? explode('.', $state->min)[0] : $state->min;
                    }
                    else {
                        return 'undefined';
                    }
                }
                // Otherwise its a status value, e.g. "1.000"
                else {
                    return $item[0];
                }
            });

            $name = $sign . ' ' . $values->join(' oder ');
        }

        return [
            'data' => [
                'id' => $this->id,
                'parent' => $this->parent?->id,
                'name' => $name,
                'model_id' => $this->model->id,
                'status_type_id' => $this->model->status_type_id,
                'action_type_id' => $this->model->action_type_id,
                'type' => 'statusrule-node',
            ],
            'classes' => 'node statusrule-node'
        ];
    }

    /**
     * @param Collection $actionTypes
     */
    public function calculate(Collection $actionTypes) {
        // TODO: Implement calculate() method.
    }
}
