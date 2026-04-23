<?php


namespace App\Graph;


use App\ProcessType\ActionType;
use Illuminate\Support\Collection;

/**
 * Class LiberalActionNode
 * @package App\Graph
 */
class LiberalActionNode extends Node {

    /**
     * Eltern-Node
     * @var Node
     */
    public $parent;

    /**
     * Id des Status-Typs, falls die Node eine Aktions- oder Statusregel hat.
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
     * Gibt die Node in im Datenformat einer Cytoscape-Node zurück.
     * @return array
     */
    public function transform(): array {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->model->name,
                'type' => 'liberal-action',
                'model_id' => $this->model->id,
                'parent' => $this->parent?->id,
                'status_type_id' => $this->statusTypeId
            ],
            'classes' => 'node action liberal-action'
        ];
    }

    /**
     * Berechnet, welche Elements (Nodes und Edges) zur der Node gehören.
     * @param Collection $actionTypes
     * @return void
     */
    public function calculate(Collection $actionTypes) {
        // TODO: Implement calculate() method.
    }
}
