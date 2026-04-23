<?php

namespace Database\Builder\Definition;

use App\ProcessType\Event;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class EventBuilder
 * @package Database\Builder
 */
class EventBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'event_name',
            'description' => '',
            'data' => []
        ];
    }

    /**
     * @param array $attributes
     * @return Event
     */
    public function make(array $attributes = []) {
        return new Event(array_merge($this->state, $attributes));
    }

}
