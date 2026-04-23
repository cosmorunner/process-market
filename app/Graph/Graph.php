<?php

namespace App\Graph;

use Illuminate\Support\Collection;

/**
 * Representation of a graph.
 */
class Graph {

    const DEFAULT_CENTER_POSITION = [
        "x" => 500,
        "y" => 150
    ];

    public Collection $nodes;
    public Collection $edges;

    /**
     * @param array $elements
     */
    public function __construct(array $elements) {
        $this->nodes = collect();
        $this->edges = collect();

        foreach ($elements as $element) {
            if ($this->isNode($element)) {
                $this->nodes->add($element);
            }
            if ($this->isEdge($element)) {
                $this->edges->add($element);
            }
        }
    }

    /**
     * Statusnodes
     * @return Collection
     */
    public function statusNodes(): Collection {
        return $this->nodes->filter(fn($node) => $this->hasClass('status', $node));
    }

    /**
     * All action nodes that do not have any action- or statusrules.
     * @return Collection
     */
    public function liberalActionNodes(): Collection {
        return $this->nodes->filter(fn($node) => $this->hasClass('liberal-action', $node));
    }

    /**
     * Calculates the center of the nodes.
     * Defaults to DEFAULT_CENTER_POSITION
     * @param Collection $nodes
     * @return array
     */
    public function centerPosition(Collection $nodes): array {
        if ($nodes->isEmpty()) {
            return self::DEFAULT_CENTER_POSITION;
        }

        $xAverage = $nodes->map(fn($node) => $node['position']['x'] ?? self::DEFAULT_CENTER_POSITION['x'])->avg();
        $yAverage = $nodes->map(fn($node) => $node['position']['y'] ?? self::DEFAULT_CENTER_POSITION['y'])->avg();

        return [
            'x' => $xAverage,
            'y' => $yAverage
        ];
    }

    /**
     * Checks for a specific class
     * @param string $className
     * @param array $element
     * @return bool
     */
    private function hasClass(string $className, array $element): bool {
        $string = $element['classes'] ?? '';
        $classes = array_map('trim', explode(' ', $string));

        return in_array($className, $classes);
    }

    /**
     * Checks if an element is a node.
     * @param array $element
     * @return bool
     */
    private function isNode(array $element): bool {
        return $this->hasClass('node', $element);
    }

    /**
     * Checks if an element is an edge.
     * @param array $element
     * @return bool
     */
    private function isEdge(array $element): bool {
        return $this->hasClass('edge', $element);
    }
}