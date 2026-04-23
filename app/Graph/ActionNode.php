<?php

namespace App\Graph;

use App\ProcessType\ActionType;
use Illuminate\Support\Collection;

/**
 * Class ActionNode
 * @package App\Graph
 */
class ActionNode extends Node {

    /**
     * Parent node
     * @var Node
     */
    public $parent;

    /**
     * Id of the status type if the node has an action or status rule.
     * @var string|null
     */
    public $statusTypeId;

    /**
     * ActionNode constructor.
     * @param ActionType $model
     * @param Node|null $parent
     * @param null $statusTypeId
     */
    public function __construct($model, Node $parent = null, $statusTypeId = null) {
        parent::__construct($model);

        $this->parent = $parent;
        $this->statusTypeId = $statusTypeId;
    }

    /**
     * Returns the node in the data format of a Cytoscape node.
     * @return array
     */
    public function transform(): array {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->model->name,
                'type' => 'action',
                'model_id' => $this->model->id,
                'parent' => $this->parent?->id,
                'status_type_id' => $this->statusTypeId
            ],
            'classes' => 'node action'
        ];
    }

    /**
     * Calculates which elements (nodes and edges) belong to the node.
     * @param Collection $actionTypes
     * @return void
     */
    public function calculate(Collection $actionTypes) {
        // ignore, required by interface
    }
}
