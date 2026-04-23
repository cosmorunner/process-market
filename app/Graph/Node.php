<?php


namespace App\Graph;

use App\ProcessType\ActionType;
use App\ProcessType\State;
use App\ProcessType\StatusRule;
use App\ProcessType\StatusType;
use Exception;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class Node
 * @package App\Graph
 */
abstract class Node implements Transformable {

    /**
     * @var string
     */
    public $id;

    /**
     * Datengrundlage der Node.
     * @var ActionType|State|StatusType|StatusRule
     */
    public $model;

    /**
     * Data-Attribut der Cy-Node.
     * @var array
     */
    public $data = [];

    /**
     * CSS-Klassen
     */
    public $classes;

    /**
     * Node constructor.
     * @param null $model
     * @param array $data
     * @param string $classes
     */
    public function __construct($model = null, array $data = [], string $classes = '') {
        try {
            $this->model = $model;
            $this->id = Uuid::uuid4()->toString();
            $this->data = $data;
            $this->classes = $classes;
        } catch (Exception){
            // Ignore
        }
    }

    /**
     * Berechnet, welche Elements (Nodes und Edges) zur der Node gehören.
     * @param Collection $actionTypes
     * @return void
     */
    public abstract function calculate(Collection $actionTypes);

    /**
     * Setzt die eigenen CSS-Klassen für die Node.
     * @param string $classes
     * @noinspection PhpUnused
     */
    public function setCustomClasses(string $classes) {
        $this->classes = $classes;
    }

    /**
     * Setzt die eigenen CSS-Klassen für die Node.
     * @param string $classes
     */
    public function addCustomClasses(string $classes) {
        $this->classes .= ' ' . $classes;
    }
}
