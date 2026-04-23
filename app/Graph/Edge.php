<?php


namespace App\Graph;


/**
 * Class Edge
 * @package App\Graph
 */
class Edge implements Transformable {

    /**
     * Cy-Id.
     * @var string
     */
    public $id;

    /**
     * Starting point of the edge.
     * @var ActionNode|StateNode|StatusNode
     */
    public $source;

    /**
     * End point of the edge.
     * @var ActionNode|StateNode|StatusNode
     */
    public $target;

    /**
     * Type of edge, e.g. action-rule-edge, status-rule-edge, initial-state-edge
     * @var string
     */
    public $type;

    /**
     * Id of the status rule or action rule, if available. Null for intiial-state-edge.
     * @var string
     */
    public $modelId;

    /**
     * Html Classes for the edge
     * @var string
     */
    public $classes;

    /**
     * Edge constructor.
     * @param Node $source
     * @param Node $target
     * @param string $type
     * @param string|null $modelId
     * @param string|null $classes
     */
    public function __construct(Node $source, Node $target, string $type, string $modelId = null, string $classes = null) {
        $this->source = $source;
        $this->target = $target;
        $this->id = $source->id . ' ' . $target->id;
        $this->type = $type;
        $this->modelId = $modelId;
        $this->classes = $classes;
    }

    /**
     * Returns the node in the data format of a Cytoscape node.
     * @return array
     */
    public function transform(): array {
        $sourceModelId = $this->source->model?->id;
        $targetModelId = $this->target->model?->id;

        return [
            'classes' => $this->classes ?? sprintf("edge %s straight", $this->type),
            'data' => [
                'id' => $this->id,
                'source' => $this->source->id,
                'source_model_id' => $sourceModelId,
                'target_model_id' => $targetModelId,
                'target' => $this->target->id,
                'type' => $this->type,
                'model_id' => $this->modelId
            ],
        ];
    }
}
