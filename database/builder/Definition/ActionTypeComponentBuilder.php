<?php /** @noinspection PhpUnused */

namespace Database\Builder\Definition;

use App\ProcessType\ActionType;
use App\ProcessType\ActionTypeComponent;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class ComponentBuilder
 * @package Database\Builder
 */
class ActionTypeComponentBuilder extends AbstractBuilder {

    public function definition(): array {
        return [
            'id' => Uuid::uuid4()->toString(),
            'label' => 'Component 1',
            'namespace' => 'allisa',
            'css_classes' => '',
            'action_type_id' => Uuid::uuid4()->toString(),
            'sort' => 0,
            'identifier' => 'form',
            'version' => '1.0.0',
            'width' => '12',
            'options' => []
        ];
    }

    /**
     * Aktionstyp zu dem die Komponente gehört.
     * @param ActionType $actionType
     * @return $this
     */
    public function ofActionType(ActionType $actionType) {
        return $this->state([
            'action_type_id' => $actionType->id
        ]);
    }

    /**
     * @param array $attributes
     * @return ActionTypeComponent
     */
    public function make(array $attributes = []) {
        return ActionTypeComponent::make(array_merge($this->state, $attributes));
    }
}
