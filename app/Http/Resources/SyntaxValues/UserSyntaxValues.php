<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\Models\User;
use App\ProcessType\ActionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserSyntaxValues
 * Alle Syntax-Werte die einem Benutzer zur Verfügung stehen.
 * @package App\Http\Resources
 */
class UserSyntaxValues extends JsonResource {

    /**
     * Graph-Model
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        /* @var User $user */
        $user = auth()->user();
        $res = $this->resource->cache();

        // Um Syntax-Werte von Prozess-Version zu laden, auf denen der Benutzer Zugriff hat.
        $publishedProcessVersionIds = $user ? $user->cache()['published_process_version_ids'] ?? [] : [];
        $licensesProcessVersionIds = $user ? $user->cache()['licenses_process_version_ids'] ?? [] : [];
        $processVersions = ProcessVersion::with(['process', 'environments'])->findMany(collect([
            ...$publishedProcessVersionIds,
            ...$licensesProcessVersionIds
        ])->unique());

        $processVersionsArrays = $processVersions->map(fn(ProcessVersion $processVersion) => $processVersion->cache());

        // Um Aktions-Daten zu laden, sofern vorhanden.
        /* @var ActionType $actionType */
        $actionType = $this->additional['actionType'] ?? null;
        $syntaxValues = [];
        $pipeValues = [];

        // Zurückzugebene Syntax-Bereiche [[]]...
        $syntaxParts = $this->additional['syntaxParts'] ?? [];

        // Zurückzugebene Pipe-Bereiche abc|...
        $pipeParts = $this->additional['pipeParts'] ?? [];

        foreach ($syntaxParts as $part) {
            $syntaxValues[$part] = $this->getSyntaxPart($part, $res, $processVersionsArrays, $request, $actionType);
        }

        foreach ($pipeParts as $part) {
            $pipeValues[$part] = $this->getPipePart($part, $res, $processVersionsArrays, $request, $actionType);
        }

        // Graph-Namespaces sortieren
        if (array_key_exists('graphs', $pipeValues)) {
            usort($pipeValues['graphs'], function ($a, $b) {
                return $a['label'] > $b['label'];
            });
        }

        return [
            'syntax' => $syntaxValues,
            'pipe' => $pipeValues,
        ];
    }

    /**
     * Gibt Werte für einen Syntax [[]] Part zurück.
     * @param string $part
     * @param array $res ProcessVersionCache
     * @param $processVersionCacheArrays
     * @param $request
     * @param $actionType
     * @return array|array[]
     */
    private function getSyntaxPart(string $part, array $res, $processVersionCacheArrays, $request, $actionType) {
        return match ($part) {
            // Graph Daten
            'process.meta' => $res['syntax_values']['syntax']['process.meta'],
            'process.outputs' => $res['syntax_values']['syntax']['process.outputs'],
            'process.artifacts' => $res['syntax_values']['syntax']['process.artifacts'],
            'process.status' => $res['syntax_values']['syntax']['process.status'],
            'process.urls' => $res['syntax_values']['syntax']['process.urls'],
            'public_apis' => $res['syntax_values']['syntax']['public_apis'],
            'variables' => $res['syntax_values']['syntax']['variables'],
            'graphs.urls' => $res['syntax_values']['syntax']['graphs.urls'],
            'reference.metas' => $res['syntax_values']['syntax']['reference.metas'],
            'reference.relation_data' => $res['syntax_values']['syntax']['reference.relation_data'],

            // Daten zu den Verknüpfungs-Referenzen.
            'reference.outputs' => (new SyntaxReferenceOutputs($res))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'reference.status' => (new SyntaxReferenceStatus($res))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'reference.urls' => (new SyntaxReferenceUrls($res))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),

            // Falls ein Aktionstyp-Kontext angegeben wurde, werden hier die Aktions-Syntax-Werte zurückgegen.
            'action.outputs' => $actionType ? (new SyntaxActionOutputs($actionType))->toArray($request) : (new SyntaxActionOutputsCollection($res))->toArray($request),
            'action.processors' => $actionType ? (new SyntaxActionProcessors($actionType))->toArray($request) : (new SyntaxActionProcessorsCollection($res))->toArray($request),
            'action.artifacts' => $actionType ? (new SyntaxActionArtifacts($actionType))->toArray($request) : (new SyntaxActionArtifactsCollection($res))->toArray($request),

            // Meta-Daten zum aktuellen Graph und externe Graphen
            'graphs.meta' => (new SyntaxGraphsMeta($res))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),

            // Globale Syntax-Werte
            'url' => (new SyntaxUrl($res))->toArray($request),
            'auth' => (new SyntaxAuth($res))->toArray($request),
            'system' => (new SyntaxSystem($res))->toArray($request),
            'date' => (new SyntaxDate($res))->toArray($request),
            default => []
        };
    }

    /**
     * Gibt Pipe-Syntax Werte zurück.
     * @param string $part
     * @param array $res
     * @param $processVersionCaches
     * @param $request
     * @param $actionType
     * @return array|array[]
     */
    private function getPipePart(string $part, array $res, $processVersionCaches, $request, $actionType) {
        $allCaches = collect([$res, ...$processVersionCaches]);

        return match ($part) {
            'environment_groups' => $res['syntax_values']['pipe']['environment_groups'],
            'environment_users' => $res['syntax_values']['pipe']['environment_users'],
            'environment_processes' => $res['syntax_values']['pipe']['environment_processes'],
            'environment_bots' => $res['syntax_values']['pipe']['environment_bots'],
            'environment_public_apis' => $res['syntax_values']['pipe']['environment_public_apis'],
            'environment_connectors' => $res['syntax_values']['pipe']['environment_connectors'],
            'environment_requests' => $res['syntax_values']['pipe']['environment_requests'],
            'environment_variables' => $res['syntax_values']['pipe']['environment_variables'],
            'roles' => $res['syntax_values']['pipe']['roles'],
            'events' => $res['syntax_values']['pipe']['events'],
            'list_configs' => $res['syntax_values']['pipe']['list_configs'],
            'relation_types' => $res['syntax_values']['pipe']['relation_types'],
            'templates' => $res['syntax_values']['pipe']['templates'],
            'action_types' => $res['syntax_values']['pipe']['action_types'],

            // Falls ein Aktionstyp-Kontext angegeben wurde, werden hier die Aktions-Syntax-Werte zurückgegen.
            'action_type_processors' => $actionType ? (new PipeActionTypeProcessors($actionType))->toArray($request) : [],

            // Daten von externen Graphen.
            'graphs' => (new PipeGraphs($allCaches))->toArray($request),
            'graphs_action_types' => (new PipeGraphActionTypes($allCaches))->toArray($request),
            'graphs_relation_types' => (new PipeGraphRelationTypes($allCaches))->toArray($request),
            default => []
        };
    }
}
