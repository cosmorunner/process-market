<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GraphValues
 * Alle Syntax-Werte die einem Benutzer zur Verfügung stehen.
 * @package App\Http\Resources
 */
class ProcessVersionSyntaxValues extends JsonResource {

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
        $res = $this->resource;
        $definition = $this->resource->definition;

        $actionOutputs = $definition->actionTypes->map(function (ActionType $actionType) use ($request) {
            return (new SyntaxActionOutputs($actionType))->toArray($request);
        })->flatten(1)->unique('value')->toArray();

        $actionProcessors = $definition->actionTypes->map(function (ActionType $actionType) use ($request) {
            return (new SyntaxActionProcessors($actionType))->toArray($request);
        })->flatten(1)->unique('value')->toArray();

        $actionTypeProcessors = $definition->actionTypes->map(function (ActionType $actionType) use ($request) {
            return (new PipeActionTypeProcessors($actionType))->toArray($request);
        })->flatten(1)->unique('value')->toArray();

        return [
            'syntax' => [
                // Graph Daten
                'process.meta' => (new SyntaxProcessMeta($res))->toArray($request),
                'process.outputs' => (new SyntaxProcessOutputs($res))->toArray($request),
                'process.artifacts' => (new SyntaxProcessArtifacts($res))->toArray($request),
                'process.actions' => (new SyntaxProcessActions($res))->toArray($request),
                'process.status' => (new SyntaxProcessStatus($res))->toArray($request),
                'process.urls' => (new SyntaxProcessUrls($res))->toArray($request),
                'action.outputs' => $actionOutputs,
                'action.processors' => $actionProcessors,
                'public_apis' => (new SyntaxPublicApis($res))->toArray($request),
                'variables' => (new SyntaxVariables($res))->toArray($request),
                'graphs.urls' => (new SyntaxGraphsUrls($res))->toArray($request),
                'graphs.meta' => (new SyntaxGraphsMeta($res))->toArray($request),
            ],
            'pipe' => [
                'environment_groups' => (new PipeEnvironmentGroups($res))->toArray($request),
                'environment_users' => (new PipeEnvironmentUsers($res))->toArray($request),
                'environment_processes' => (new PipeEnvironmentProcesses($res))->toArray($request),
                'environment_bots' => (new PipeEnvironmentBots($res))->toArray($request),
                'environment_public_apis' => (new PipeEnvironmentPublicApis($res))->toArray($request),
                'environment_connectors' => (new PipeEnvironmentConnectors($res))->toArray($request),
                'environment_requests' => (new PipeEnvironmentRequests($res))->toArray($request),
                'environment_variables' => (new PipeEnvironmentVariables($res))->toArray($request),
                'roles' => (new PipeRoles($res))->toArray($request),
                'events' => (new PipeEvents($res))->toArray($request),
                'list_configs' => (new PipeListConfigs($res))->toArray($request),
                'relation_types' => (new PipeRelationTypes($res))->toArray($request),
                'templates' => (new PipeTemplates($res))->toArray($request),
                'action_types' => (new PipeActionTypes($res))->toArray($request),
                'action_type_processors' => $actionTypeProcessors,
            ],
        ];
    }
}
