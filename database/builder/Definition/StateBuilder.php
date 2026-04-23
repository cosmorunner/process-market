<?php

namespace Database\Builder\Definition;

use App\ProcessType\State;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class StateBuilder
 * @package Database\Builder\Definition
 */
class StateBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'status_type_id' => Uuid::uuid4(),
            'description' => 'Default description',
            'image' => 'arrow_forward',
            'min' => '0.000',
            'max' => '0.000',
            'hidden' => false,
            'color' => '#72c6ff'
        ];
    }

    /**
     * @param array $attributes
     * @return State
     */
    public function make(array $attributes = []) {
        return new State(array_merge($this->state, $attributes));
    }

    /**
     * @param string $min
     * @return $this
     */
    public function withMin(string $min) {
        return $this->state([
            'min' => $min
        ]);
    }

    /**
     * @param string $max
     * @return $this
     */
    public function withMax(string $max) {
        return $this->state([
            'max' => $max
        ]);
    }

    /**
     * @param string $id
     * @return $this
     * @noinspection PhpUnused
     */
    public function withStatusTypeId(string $id) {
        return $this->state([
            'status_type_id' => $id
        ]);
    }

    /**
     * Sets the default initial state used when creating a new statustype.
     * @return StateBuilder
     */
    public function defaultInitial(string $minMax, string $statusTypeId) {
        return $this->state([
            'color' => "#72c6ff",
            'description' => "Start",
            'image' => "arrow_forward",
            'min' => $minMax,
            'max' => $minMax,
            'hidden' => false,
            'status_type_id' => $statusTypeId
        ]);
    }

}
