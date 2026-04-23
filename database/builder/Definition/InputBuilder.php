<?php

namespace Database\Builder\Definition;

use App\ProcessType\Input;
use Database\Builder\AbstractBuilder;

/**
 * Class InputBuilder
 * @package Database\Builder\Definition
 */
class InputBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'name' => 'input_1',
            'value' => null,
            'type' => 'basic',
            'type_options' => []
        ];
    }

    /**
     * @param array $attributes
     * @return Input
     */
    public function make(array $attributes = []) {
        $input = new Input(array_merge($this->state, $attributes));
        $input->type = Input::identifyType($input->value);

        return $input;
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
     * @param string|array $value
     * @return $this
     */
    public function withValue(string|array $value) {
        return $this->state([
            'value' => $value
        ]);
    }

    /**
     * @return $this
     */
    public function withTypeJsonArray() {
        return $this->state([
            'type' => Input::TYPE_ARRAY
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
