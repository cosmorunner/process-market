<?php

namespace Database\Builder\Definition;

use App\Enums\SmartStatusTypes;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Database\Builder\AbstractBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Ramsey\Uuid\Uuid;

/**
 * Class StatusTypeBuilder
 * @package Database\Builder\Definition
 */
class StatusTypeBuilder extends AbstractBuilder {

    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'reference' => 'hauptstatus',
            'auto_reference' => false,
            'name' => 'Hauptstatus',
            'description' => 'Fortschritt des Issues.',
            'namespace' => 'allisa',
            'options' => [],
            'smart' => [],
            'identifier' => 'simple',
            'version' => '1.0.0',
            'image' => 'flag',
            'sort' => 1,
            'size' => '4x1',
            'default' => '-1.000',
            'hidden' => false,
            'states' => []
        ];
    }

    /**
     * @param array $attributes
     * @return StatusType
     */
    public function make(array $attributes = []) {
        $statusType = new StatusType(array_merge($this->state, $attributes));
        $statusType->states = $statusType->states->map(function (State $state) use ($statusType) {
            $state->status_type_id = $statusType->id;

            return $state;
        });

        return $statusType;
    }

    /**
     * @param array $states
     * @return $this
     * @throws BindingResolutionException
     */
    public function withStates(array $states) {
        foreach ($states as $index => $state) {
            // When using an integer, min and max is same.
            if (is_integer($state)) {
                $states[$index] = app(StateBuilder::class)->withMin($state)->withMax($state)->make();
            }
            // When using an array ([0, 10], first value is used as min, second value as max.
            if (is_array($state) && Arr::isList($state) && count($state) === 2) {
                $states[$index] = app(StateBuilder::class)->withMin($state[0])->withMax($state[1])->make();
            }
        }

        return $this->state([
            'states' => $this->convertObjectToArray($states)
        ]);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function withSmartRelationType(array $options) {
        return $this->state([
            'smart' => [
                'type' => SmartStatusTypes::RelationType->value,
                'options' => $options
            ]
        ]);
    }

    /**
     * @param array $options
     * @return StatusTypeBuilder
     */
    public function withSmartCustomLogic(array $options) {
        return $this->state([
            'smart' => [
                'type' => SmartStatusTypes::CustomLogic->value,
                'options' => $options
            ]
        ]);
    }

    /**
     * @param array $options
     * @return StatusTypeBuilder
     * @noinspection PhpUnused
     */
    public function withSmartRelatedStatus(array $options) {
        return $this->state([
            'smart' => [
                'type' => SmartStatusTypes::RelatedStatus->value,
                'options' => $options
            ]
        ]);
    }

    /**
     * @param string $reference
     * @return $this
     */
    public function withReference(string $reference) {
        return $this->state([
            'reference' => $reference
        ]);
    }

    public function withInitialValue(string $defaultValue) {
        return $this->state([
            'default' => $defaultValue
        ]);
    }
}
