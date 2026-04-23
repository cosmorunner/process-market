<?php

namespace Database\Builder\Definition;

use App\ProcessType\ActionRule;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class ActionRuleBuilder
 * @package Database\Builder\Definition
 */
class ActionRuleBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'action_type_id' => Uuid::uuid4(),
            'status_type_id' => Uuid::uuid4(),
            'type' => ActionRule::TYPE_STATUS,
            'operator' => ActionRule::OPERATOR_IN_ARRAY,
            'values' => [
                '1.000',
                '8.000'
            ],
            'state_ids' => [],
            'group' => 'group_1'
        ];
    }

    /**
     * @param array $attributes
     * @return ActionRule
     */
    public function make(array $attributes = []) {
        return new ActionRule(array_merge($this->state, $attributes));
    }

    /**
     * Statustyp der Aktionsregel.
     * @param string $statusTypeId
     * @return $this
     */
    public function withStatusType(string $statusTypeId) {
        return $this->state([
            'status_type_id' => $statusTypeId
        ]);
    }

}
