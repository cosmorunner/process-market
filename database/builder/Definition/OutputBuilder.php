<?php

namespace Database\Builder\Definition;

use App\ProcessType\Output;
use Database\Builder\AbstractBuilder;

/**
 * Class OutputBuilder
 * @package Database\Builder\Definition
 */
class OutputBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'name' => 'output_1',
            'description' => '',
            'default' => null,
            'type' => 'basic',
            'type_options' => [],
            'validation' => [],
        ];
    }

    /**
     * @param array $attributes
     * @return Output
     */
    public function make(array $attributes = []) {
        $output = new Output(array_merge($this->state, $attributes));
        $output->type = Output::identifyType($output->default);

        return $output;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function withName(string $name) {
        return $this->state([
            'name' => $name
        ]);
    }

    /**
     * @param string|array $default
     * @return $this
     */
    public function withDefault(string|array $default) {
        return $this->state([
            'default' => $default
        ]);
    }

    /**
     * @param array $validation
     * @return $this
     */
    public function withValidation(array $validation) {
        return $this->state([
            'validation' => $validation
        ]);
    }

    /**
     * @return $this
     */
    public function withTypeJsonArray() {
        return $this->state([
            'type' => Output::TYPE_ARRAY,
            'default' => []
        ]);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function withTypeOptions(array $options) {
        return $this->state([
            'type_options' => $options
        ]);
    }

}
