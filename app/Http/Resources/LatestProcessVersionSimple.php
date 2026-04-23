<?php

namespace App\Http\Resources;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\ListConfig;
use App\ProcessType\Output;
use App\ProcessType\RelationType;
use App\ProcessType\Role;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Einfache Repräsentation der aktuellsten Version eines Prozesses.
 * Siehe: Version ist "latest".
 * Class LatestProcessVersionSimple
 * @package App\Http\Resources
 */
class LatestProcessVersionSimple extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $namespace = $this->resource->process->namespace;
        $identifier = $this->resource->process->identifier;
        $definition = $this->resource->definition;

        $roles = $definition->roles->map(fn(Role $role) => [
            'id' => $role->id,
            'name' => $role->name
        ])->toArray();

        $outputs = $definition->outputs->map(fn(Output $output) => [
            'name' => $output->name,
            'description' => $output->description,
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
            'outputs' => $actionType->outputs->map(fn(Output $output) => [
                'name' => $output->name,
                'description' => $output->description
            ])
        ])->toArray();

        $statusTypes = $definition->statusTypes->map(fn(StatusType $statusType) => [
            'id' => $statusType->id,
            'reference' => $statusType->reference,
            'name' => $statusType->name,
            'states' => $statusType->states->map(fn(State $state) => [
                'id' => $state->id,
                'description' => $state->description,
                'min' => $state->min,
                'max' => $state->max
            ])
        ])->toArray();

        $relationTypes = $definition->relationTypes->map(fn(RelationType $relationType) => [
            'id' => $relationType->id,
            'name' => $relationType->name,
        ])->toArray();

        return [
            'namespace' => $namespace,
            'identifier' => $identifier,
            'created_at' => $this->resource->created_at,
            'full_namespace' => $namespace . '/' . $identifier . '@' . 'latest',
            'version' => 'latest',
            'roles' => $roles,
            'outputs' => $outputs,
            'list_configs' => $listConfigs,
            'action_types' => $actionTypes,
            'on_create_action_type_id' => $definition->action_type_mappings['on_create_action_type_id'] ?? null,
            'status_types' => $statusTypes,
            'relation_types' => $relationTypes
        ];

    }
}
