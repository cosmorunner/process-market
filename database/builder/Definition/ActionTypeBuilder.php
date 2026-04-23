<?php

namespace Database\Builder\Definition;

use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\ActionTypeComponent;
use App\ProcessType\Category;
use App\ProcessType\Processor;
use App\ProcessType\StatusRule;
use Database\Builder\AbstractBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Ramsey\Uuid\Uuid;

/**
 * Class ActionTypeBuilder
 * @package Database\Builder\Definition
 */
class ActionTypeBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'category_id' => Uuid::uuid4(),
            'name' => 'New ActionType',
            'reference' => 'action_type_ref',
            'description' => '',
            'image' => 'flash_on',
            'sort' => 0,
            'instant' => false,
            'show_save_button' => true,
            'inputs' => [],
            'outputs' => [],
            'action_rules' => [],
            'status_rules' => [],
            'processors' => [],
            'components' => [],
            'save_label' => 'app.execute'
        ];
    }

    /**
     * @param array $attributes
     * @return ActionType
     */
    public function make(array $attributes = []) {
        $actionType = new ActionType(array_merge($this->state, $attributes));

        $actionType->actionRules = $actionType->actionRules->map(function (ActionRule $rule) use ($actionType) {
            $rule->action_type_id = $actionType->id;

            return $rule;
        });

        $actionType->statusRules = $actionType->statusRules->map(function (StatusRule $rule) use ($actionType) {
            $rule->action_type_id = $actionType->id;

            return $rule;
        });

        $actionType->processors = $actionType->processors->map(function (Processor $processor) use ($actionType) {
            $processor->action_type_id = $actionType->id;

            return $processor;
        });

        $actionType->components = $actionType->components->map(function (ActionTypeComponent $component) use ($actionType) {
            $component->action_type_id = $actionType->id;

            return $component;
        });

        return $actionType;
    }

    /**
     * Aktionstyp-Inputs.
     * @param array $inputs
     * @return $this
     */
    public function withInputs(array $inputs) {
        return $this->state([
            'inputs' => $this->convertObjectToArray($inputs)
        ]);
    }

    /**
     * Aktionstyp-Outputs.
     * @param array $outputs
     * @return $this
     */
    public function withOutputs(array $outputs) {
        return $this->state([
            'outputs' => $this->convertObjectToArray($outputs)
        ]);
    }

    /**
     * Aktionstyp-Aktionsregeln
     * @param array $actionRules
     * @return $this
     */
    public function withActionRules(array $actionRules) {
        return $this->state([
            'action_rules' => $this->convertObjectToArray($actionRules)
        ]);
    }

    /**
     * Aktionstyp-Statusregeln
     * @param array $statusRules
     * @return $this
     */
    public function withStatusRules(array $statusRules) {
        return $this->state([
            'status_rules' => $this->convertObjectToArray($statusRules)
        ]);
    }

    /**
     * Aktionstyp-Prozessoren
     * @param array $processors
     * @return $this
     */
    public function withProcessors(array $processors) {
        return $this->state([
            'processors' => $this->convertObjectToArray($processors)
        ]);
    }

    /**
     * Aktionstyp-Komponenten
     * @param array $components
     * @return $this
     */
    public function withComponents(array $components) {
        return $this->state([
            'components' => $this->convertObjectToArray($components)
        ]);
    }

    /**
     * Set category of action type.
     * @return ActionTypeBuilder
     */
    public function ofCategory(Category $category) {
        return $this->state([
            'category_id' => $category->id
        ]);
    }

    /**
     * Fügt einen Aktionstyp mit einem bestimmten Prozessor hinzu.
     * @param $identifier
     * @param $processorOptions
     * @param array $outputs Optionale Angabe von Actiontype-Outputs
     * @return $this
     * @throws BindingResolutionException
     */
    public function withProcessor($identifier, $processorOptions = [], array $outputs = []) {
        $processor = app(ProcessorBuilder::class)
            ->withIdentifier($identifier)
            ->withActionTypeId($this->state['id'])
            ->withOptions($processorOptions)
            ->make();

        $this->state['outputs'] = array_merge($this->state['outputs'], $this->convertObjectToArray($outputs));
        $this->state['processors'] = array_merge($this->state['processors'], [$processor->toArray()]);

        return $this;
    }

}
