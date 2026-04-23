<?php


namespace App\Graph;


/**
 *
 */
interface Transformable {

    /**
     * Gibt die Node im Datenformat einer Cytoscape-Node/Edge zurück.
     * @return array
     */
    public function transform(): array;
}
