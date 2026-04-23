<?php

namespace Database\Builder\Definition;

use App\ProcessType\StatusRule;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class StatusRuleBuilder
 * @package Database\Builder\Definition
 */
class StatusRuleBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'action_type_id' => Uuid::uuid4(),
            'status_type_id' => Uuid::uuid4(),
            'operator' => StatusRule::OPERATOR_SET,
            'conditions' => [],
            'output' => null,
            'values' => ['1.000']
        ];
    }

    /**
     * @param array $attributes
     * @return StatusRule
     */
    public function make(array $attributes = []) {
        return new StatusRule(array_merge($this->state, $attributes));
    }

    /**
     * @param string $statusTypeId
     * @return $this
     */
    public function withStatusType(string $statusTypeId) {
        return $this->state([
            'status_type_id' => $statusTypeId
        ]);
    }
}
