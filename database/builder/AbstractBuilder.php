<?php

namespace Database\Builder;

/**
 * Class AbstractBuilder
 * @package Database\Builder
 */
abstract class AbstractBuilder {

    protected array $state;

    public function __construct() {
        $this->state = $this->definition();
    }

    /**
     * Changes some part of the object definition.
     * @param array $data
     * @return $this
     */
    protected function state(array $data) {
        $this->state = array_merge($this->state, $data);

        return $this;
    }

    /**
     * @param array $arrayOfObjects
     * @return array
     */
    protected function convertObjectToArray(array $arrayOfObjects) {
        return array_map(fn($object) => is_object($object) ? $object->toArray() : $object, $arrayOfObjects);
    }

    /**
     * Makes an object.
     * @param array $attributes
     * @return mixed
     */
    abstract public function make(array $attributes = []);

    /**
     * Defines object properties as array.
     * @return array
     */
    abstract public function definition(): array;
}