<?php

namespace Database\Builder\Definition;

use App\ProcessType\Processor;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class ProcessorBuilder
 * @package Database\Builder\Definition
 */
class ProcessorBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'action_type_id' => Uuid::uuid4(),
            'identifier' => 'execute_action',
            'conditions' => [],
            'options' => [],
            'required' => true,
            'sort' => null
        ];
    }

    /**
     * @param array $attributes
     * @return Processor
     */
    public function make(array $attributes = []) {
        return new Processor(array_merge($this->state, $attributes));
    }

    /**
     * @param string $actionTypeId
     * @return $this
     */
    public function withActionTypeId(string $actionTypeId) {
        return $this->state([
            'action_type_id' => $actionTypeId
        ]);
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function withIdentifier(string $identifier) {
        return $this->state([
            'identifier' => $identifier
        ]);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function withOptions(array $options) {
        return $this->state([
            'options' => $options
        ]);
    }
}
