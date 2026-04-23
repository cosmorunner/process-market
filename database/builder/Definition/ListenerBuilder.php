<?php

namespace Database\Builder\Definition;

use App\ProcessType\Listener;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class ListenerBuilder
 * @package Database\Builder
 */
class ListenerBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'description' => 'description.',
            'events' => [],
            'relation_type' => [],
            'conditions' => [],
            'type' => 'execute_action',
            'type_options' => [
                'action_type' => '',
                'mapping' => []
            ]
        ];
    }

    /**
     * @param array $attributes
     * @return Listener
     */
    public function make(array $attributes = []) {
        return new Listener(array_merge($this->state, $attributes));
    }

    /**
     * @param array $typeOptions
     * @return $this
     */
    public function withTypeOptions(array $typeOptions) {
        return $this->state([
            'type_options' => $typeOptions
        ]);
    }

    /**
     * @param array $events
     * @return $this
     */
    public function withEvents(array $events) {
        return $this->state([
            'events' => $events
        ]);
    }

}
