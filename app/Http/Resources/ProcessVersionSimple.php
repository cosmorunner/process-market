<?php

namespace App\Http\Resources;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Category;
use App\ProcessType\Event;
use App\ProcessType\Input;
use App\ProcessType\ListConfig;
use App\ProcessType\Listener;
use App\ProcessType\Output;
use App\ProcessType\Processor;
use App\ProcessType\RelationType;
use App\ProcessType\Role;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProcessVersionSimple
 * @package App\Http\Resources
 */
class ProcessVersionSimple extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $definition = $this->resource->definition;
        $namespace = $definition->namespace;
        $identifier = $definition->identifier;

        $roles = $definition->roles->map(fn(Role $role) => [
            'id' => $role->id,
            'name' => $role->name
        ])->toArray();

        $outputs = $definition->outputs->map(fn(Output $output) => [
            'name' => $output->name,
            'description' => $output->description,
            'type' => $output->type
        ])->toArray();

        $categories = $definition->categories->map(fn(Category $category) => [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'sort' => $category->sort
        ])->toArray();

        $listConfigs = $definition->listConfigs->map(fn(ListConfig $listConfig) => [
            'id' => $listConfig->id,
            'name' => $listConfig->name,
            'description' => $listConfig->description,
            'slug' => $listConfig->slug,
            'template' => $listConfig->template
        ])->toArray();

        $actionTypes = $definition->actionTypes->map(fn(ActionType $actionType) => [
            'id' => $actionType->id,
            'name' => $actionType->name,
            'reference' => $actionType->reference,
            'outputs' => $actionType->outputs->map(fn(Output $output) => [
                'name' => $output->name,
                'description' => $output->description
            ])->toArray(),
            'inputs' => $actionType->inputs->map(fn(Input $input) => [
                'name' => $input->name,
            ])->toArray(),
            'processors' => $actionType->processors->map(fn(Processor $processor) => [
                'id' => $processor->id,
                'identifier' => $processor->identifier
            ])->toArray(),
        ])->toArray();

        $statusTypes = $definition->statusTypes->map(fn(StatusType $statusType) => [
            'id' => $statusType->id,
            'reference' => $statusType->reference,
            'name' => $statusType->name,
            'smart' => $statusType->isSmartStatus(),
            'states' => $statusType->states->map(fn(State $state) => [
                'id' => $state->id,
                'description' => $state->description,
                'min' => $state->min,
                'max' => $state->max
            ])->toArray()
        ])->toArray();

        $relationTypes = $definition->relationTypes->map(fn(RelationType $relationType) => [
            'id' => $relationType->id,
            'name' => $relationType->name,
            'default' => $relationType->default,
            'connection_type' => $relationType->connection_type,
            'reference' => $relationType->reference
        ])->toArray();

        $events = $definition->events->map(fn(Event $event) => [
            'id' => $event->id,
            'name' => $event->name,
            'description' => $event->description
        ])->toArray();

        $listeners = $definition->listeners->map(fn(Listener $listener) => [
            'id' => $listener->id,
            'events' => $listener->events,
            'description' => $listener->description
        ])->toArray();

        return [
            'namespace' => $namespace,
            'identifier' => $identifier,
            'created_at' => $this->resource->created_at->toString(),
            'full_namespace' => $namespace . '/' . $identifier . '@' . $this->resource->version,
            'full_namespace_without_version' => $namespace . '/' . $identifier,
            'version' => $this->resource->version,
            'roles' => $roles,
            'categories' => $categories,
            'outputs' => $outputs,
            'list_configs' => $listConfigs,
            'action_types' => $actionTypes,
            'status_types' => $statusTypes,
            'relation_types' => $relationTypes,
            'events' => $events,
            'listeners' => $listeners
        ];

    }
}
