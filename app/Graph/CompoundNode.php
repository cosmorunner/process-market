<?php


namespace App\Graph;

use App\ProcessType\StatusType;
use Illuminate\Support\Collection;

/**
 * Class CompoundNode
 * @package App\Graph
 */
class CompoundNode extends Node {

    use UsesParenthood;

    /**
     * Id der Eltern-Node
     * @var string
     */
    public $parent;

    /**
     * Relevanz der Node, wird für die Berechnung der CompoundNodes benötigt.
     * @var int
     */
    public $relevance = 0;

    /**
     * CompoundNode constructor.
     * @param null $model
     * @param Node|null $parent
     * @param array $data
     * @param string $classes
     */
    public function __construct($model = null, Node $parent = null, array $data = [], string $classes = '') {
        parent::__construct($model, $data, $classes);

        $this->parent = $parent;
    }

    /**
     * Gibt die Node in im Datenformat einer Cytoscape-Node zurück.
     * @return array
     */
    public function transform(): array {
        return [
            'data' => array_merge([
                'id' => $this->id,
                'parent' => $this->parent ? $this->parent->id : null,
                'name' => '',
                'status_type_id' => $this->model instanceof StatusType ? $this->model->id : null,
                'type' => 'compound',
            ], $this->data),
            'classes' => 'node compound ' . $this->classes
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
