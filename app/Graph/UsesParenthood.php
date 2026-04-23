<?php

namespace App\Graph;

use Illuminate\Support\Collection;

/**
 * Gibt an, dass die Klasse selbst Child-Nodes haben kann.
 * Z.b. hat eine Status-Node mehrere State-Nodes als Children.
 * Trait UsesParenthood
 * @package App\Graph\Cytoscape
 */
trait UsesParenthood {

    /**
     * @var Collection|Node[]
     */
    private $nodes;

    /**
     * @var Collection|Edge[]
     */
    private $edges;

    /**
     * @var Collection|CompoundNode[]
     */
    private $compoundNodes;

    /**
     * Fügt eine Child-Node zur Node hinzu. Die Child-Node hat das "parent"-Attribut mit der Id.
     * @param Node $node
     * @return Node
     */
    public function addNode(Node $node) {
        if (!$this->nodes) {
            $this->nodes = collect();
        }

        if ($node instanceof StateNode || ($node instanceof ActionNode)) {
            $node->parent = $this;
        }

        $this->nodes->push($node);

        return $node;
    }

    /**
     * Entfernt eine Node aus der Collection.
     * @param Node $node
     */
    public function removeNode(Node $node) {
        $this->nodes = $this->nodes()->filter(function (Node $item) use ($node) {
            return $item->model->id !== $node->model->id;
        });
    }

    /**
     * Entfernt mehrere Nodes.
     * @param Collection $nodes
     */
    public function removeNodes(Collection $nodes) {
        foreach ($nodes as $node) {
            $this->removeNode($node);
        }
    }

    /**
     * Fügt mehrere Nodes der Node hinzu.
     * @param Collection $nodes
     */
    public function addNodes(Collection $nodes) {
        $nodes->each(function ($node) {
            if ($node instanceof Node) {
                $this->addNode($node);
            }
        });
    }

    /**
     * Fügt eine CompoundNode zur Node hinzu. Die Node hat das "parent"-Attribut mit der Id.
     * @param CompoundNode $node
     * @return void
     */
    public function addCompoundNode(CompoundNode $node) {
        if (!$this->compoundNodes) {
            $this->compoundNodes = collect();
        }

        $node->parent = $this;
        $this->compoundNodes->push($node);
    }

    /**
     * Entfernt eine CompoundNode
     * @param CompoundNode $node
     */
    public function removeCompoundNode(CompoundNode $node) {
        $this->compoundNodes = $this->compoundNodes->filter(function (CompoundNode $item) use ($node) {
            return $item->id !== $node->id;
        });
    }


    /**
     * Fügt eine Child-Edge zur Node hinzu.
     * @param Edge $edge
     * @return Edge
     */
    public function addEdge(Edge $edge) {
        if (!$this->edges) {
            $this->edges = collect();
        }

        $ids = $this->edges->pluck('id');

        if (!$ids->contains($edge->id)) {
            $this->edges->push($edge);
        }

        return $edge;
    }

    /**
     * Gibt alle Nodes zurück.
     * @return Node[]|Collection
     */
    public function nodes() {
        if (!$this->nodes) {
            $this->nodes = collect();
        }

        return $this->nodes;
    }

    /**
     * Gibt alle Compound-Nodes zurück.
     * @return CompoundNode[]|Collection
     */
    public function compoundNodes() {
        if (!$this->compoundNodes) {
            $this->compoundNodes = collect();
        }

        return $this->compoundNodes;
    }

    /**
     * Gibt alle Edges zurück.
     * @return Edge[]|Collection
     */
    public function edges() {
        if (!$this->edges) {
            $this->edges = collect();
        }

        return $this->edges;
    }


    /**
     * Entfernt eine Edge aus der Collection.
     * @param Edge $edge
     */
    public function removeEdge(Edge $edge) {
        $this->edges = $this->edges()->filter(function (Edge $item) use ($edge) {
            return $item->id !== $edge->id;
        });
    }

    /**
     * Entfernt mehrere Edges.
     * @param Collection $edges
     */
    public function removeEdges(Collection $edges) {
        foreach ($edges as $edge) {
            $this->removeEdge($edge);
        }
    }

    /**
     * Relevanz erhöhen
     * @param int $by
     */
    public function increaseRelevance(int $by) {
        $this->relevance += $by;
    }

    /**
     * Gibt eine Node anhand ihrer Status-Id zurück
     * @return StateNode|null
     * @var string $id
     */
    public function nodeByStateId(string $id) {
        return $this->nodes()->first(function (Node $node) use ($id) {
            return $node instanceof StateNode && $node->model->id === $id;
        });
    }

    /**
     * Gibt alle StateNodes zurück.
     * @param bool $withNested
     * @return Node[]|Collection
     */
    public function stateNodes($withNested = false) {
        $stateNodes = $this->nodes()->filter(function ($ele) {
            return $ele instanceof StateNode;
        });

        if ($withNested) {
            foreach ($this->compoundNodes() as $compoundNode) {
                $stateNodes = $stateNodes->merge($compoundNode->stateNodes($withNested));
            }
        }

        return $stateNodes;
    }

    /**
     * Gibt alle ActionNodes zurück.
     * @return Collection
     */
    public function actionNodes() {
        return $this->nodes()->filter(function ($ele) {
            return $ele instanceof ActionNode;
        });
    }

    /**
     * Gibt alle LiberalActionNodes zurück.
     * @return Collection
     */
    public function liberalActionNodes() {
        return $this->nodes()->filter(function ($ele) {
            return $ele instanceof LiberalActionNode;
        });
    }

    /**
     * Anzahl der Nodes.
     * @return int
     */
    public function nodesCount() {
        return $this->nodes()->count();
    }

    /**
     * Anzahl der CompoundNodes.
     * @return int
     */
    public function compoundNodesCount() {
        return $this->compoundNodes()->count();
    }

    /**
     * Anzahl der Kanten.
     * @return int
     */
    public function edgesCount() {
        return $this->edges()->count();
    }
}
