<?php


namespace App\Graph;

use App\ProcessType\State;
use Illuminate\Support\Collection;

/**
 * Class StateNode
 * @package App\Graph
 */
class StateNode extends Node {

    /**
     * Eltern-Node
     * @var Node
     */
    public $parent;

    /**
     * StateNode constructor.
     * @param State $model
     * @param Node|null $parent
     */
    public function __construct($model, Node $parent = null) {
        parent::__construct($model);
        $this->parent = $parent;
    }

    /**
     * Gibt die Node in im Datenformat einer Cytoscape-Node zurück.
     * @return array
     */
    public function transform(): array {
        return [
            'data' => [
                'id' => $this->id,
                'parent' => $this->parent?->id,
                'name' => $this->model->description,
                'model_id' => $this->model->id,
                'status_type_id' => $this->model->status_type_id,
                'type' => 'state',
            ],
            'classes' => 'node state ' . $this->classes
        ];
    }

    /**
     * @param Collection $actionTypes
     */
    public function calculate(Collection $actionTypes) {
        // TODO: Implement calculate() method.
    }
}
