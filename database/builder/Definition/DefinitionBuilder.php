<?php /** @noinspection PhpUnused */

namespace Database\Builder\Definition;

use App\ProcessType\Definition;
use Database\Builder\AbstractBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class DefinitionBuilder
 * @package Database\Builder\Definition
 */
class DefinitionBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'name' => 'Leerer Prozesstyp',
            'description' => '',
            'namespace' => 'allisa',
            'identifier' => substr(Uuid::uuid4(), 0, 8),
            'image' => 'star',
            'version' => '1.0.0',
            'definition_version' => '1.0.0',
            'reference_pattern' => '',
            'default_role_id' => null,
            'public_role_id' => null,
            'published_at' => null,
            'history_list_config_slug' => null,
            'outputs' => [],
            'status_types' => [],
            'action_types' => [],
            'roles' => [],
            'events' => [],
            'list_configs' => [],
            'menu_items' => [],
            'templates' => [],
            'relation_types' => [],
            'categories' => [],
            'listeners' => [],
            'dependencies' => []
        ];
    }

    /**
     * @param array $attributes
     * @return Definition
     */
    public function make(array $attributes = []) {
        return new Definition(array_merge($this->state, $attributes));
    }

    /**
     * @param array $statusTypes
     * @return $this
     */
    public function withStatusTypes(array $statusTypes) {
        return $this->state([
            'status_types' => $this->convertObjectToArray($statusTypes)
        ]);
    }

    /**
     * @return $this
     */
    public function withAnyStatusTypes(int $count = 1) {
        $statusTypes = Collection::times($count, fn() => app(StatusTypeBuilder::class)->make())->toArray();

        return $this->state([
            'status_types' => $this->convertObjectToArray($statusTypes)
        ]);
    }

    /**
     * @param array $actionTypes
     * @return $this
     */
    public function withActionTypes(array $actionTypes) {
        return $this->state([
            'action_types' => $this->convertObjectToArray($actionTypes)
        ]);
    }

    /**
     * @param array $outputs
     * @return $this
     */
    public function withOutputs(array $outputs) {
        return $this->state([
            'outputs' => $this->convertObjectToArray($outputs)
        ]);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function withRoles(array $roles) {
        return $this->state([
            'roles' => $this->convertObjectToArray($roles)
        ]);
    }

    /**
     * Adds the "Maintainer" role (full access).
     * @return $this
     * @throws BindingResolutionException
     */
    public function withMaintainerRole() {
        $maintainer = app(RoleBuilder::class)->withAllPermissions()->make(['name' => 'Maintainer']);

        return $this->state([
            'roles' => $this->convertObjectToArray([$maintainer])
        ]);
    }

    /**
     * @param array $listConfigs
     * @return $this
     */
    public function withListConfigs(array $listConfigs) {
        return $this->state([
            'list_configs' => $this->convertObjectToArray($listConfigs)
        ]);
    }

    /**
     * @param array $menuItems
     * @return $this
     */
    public function withMenuItems(array $menuItems) {
        return $this->state([
            'menu_items' => $this->convertObjectToArray($menuItems)
        ]);
    }

    /**
     * @param array $templates
     * @return $this
     */
    public function withTemplates(array $templates) {
        return $this->state([
            'templates' => $this->convertObjectToArray($templates)
        ]);
    }

    /**
     * @param array $relationTypes
     * @return $this
     */
    public function withRelationTypes(array $relationTypes) {
        return $this->state([
            'relation_types' => $this->convertObjectToArray($relationTypes)
        ]);
    }

    /**
     * @param array $categories
     * @return $this
     */
    public function withCategories(array $categories) {
        return $this->state([
            'categories' => $this->convertObjectToArray($categories)
        ]);
    }

    /**
     * @param array $events
     * @return $this
     */
    public function withEvents(array $events) {
        return $this->state([
            'events' => $this->convertObjectToArray($events)
        ]);
    }

    /**
     * @param array $listeners
     * @return $this
     */
    public function withListeners(array $listeners) {
        return $this->state([
            'listeners' => $this->convertObjectToArray($listeners)
        ]);
    }

    /**
     * Fügt einen simplen Aktionstyp hinzu.
     * @return $this
     * @throws BindingResolutionException
     */
    public function withSimpleActionType() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $this->state['action_types'] = array_merge($this->state['action_types'], [$actionType->toArray()]);

        return $this;
    }

    /**
     * Fügt einen Aktionstyp mit einem bestimmten Prozessor hinzu.
     * @param $identifier
     * @param $processorOptions
     * @param array $outputs Optionale Angabe von Actiontype-Outputs
     * @param bool $required
     * @return $this
     * @throws BindingResolutionException
     */
    public function withProcessorActionType($identifier, $processorOptions = [], array $outputs = [], bool $required = true) {
        $processor = app(ProcessorBuilder::class)
            ->withIdentifier($identifier)
            ->withOptions($processorOptions)
            ->make(['required' => $required]);

        $actionType = app(ActionTypeBuilder::class)->withProcessors([$processor])->withOutputs($outputs)->make();
        $this->state['action_types'] = array_merge($this->state['action_types'], [$actionType->toArray()]);

        return $this;
    }
}
