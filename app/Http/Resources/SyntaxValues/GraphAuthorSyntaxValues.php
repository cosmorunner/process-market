<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\Organisation;
use App\Models\ProcessVersion;
use App\Models\User;
use App\ProcessType\ActionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GraphAuthorSyntaxValues
 * Alle Syntax-Werte von einem Graph und dessen Author.
 * @package App\Http\Resources
 */
class GraphAuthorSyntaxValues extends JsonResource {

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
        /* @var User|Organisation $author */
        $author = $this->resource->process->author;
        $cache = $this->resource->cache();

        $processVersionCacheArrays = $author->accessiblePublishedProcessVersions()
            ->filter(fn(ProcessVersion $processVersion) => $processVersion->namespace !== $this->resource->namespace)
            ->sortBy('namespace')
            ->map(fn(ProcessVersion $processVersion) => $processVersion->cache());

        // Um Aktions-Daten zu laden, sofern vorhanden.
        /* @var ActionType $actionType */
        $actionType = $this->additional['actionType'] ?? null;
        $syntaxValues = [];
        $pipeValues = [];

        // Returned syntax sections [[]]...
        $syntaxParts = $this->additional['syntaxParts'] ?? [];

        // Returned pipe sections abc|...
        $pipeParts = $this->additional['pipeParts'] ?? [];

        foreach ($syntaxParts as $part) {
            $syntaxValues[$part] = $this->getSyntaxPart($part, $cache, $processVersionCacheArrays, $request, $actionType);
        }

        foreach ($pipeParts as $part) {
            $pipeValues[$part] = $this->getPipePart($part, $cache, $processVersionCacheArrays, $request, $actionType);
        }

        // Sort process namespace
        if (array_key_exists('graphs', $pipeValues)) {
            usort($pipeValues['graphs'], fn($a, $b) => $a['label'] > $b['label']);
        }

        // Filter by search value
        $searchParts = array_map('trim', explode(' ', (string) $this->additional['search']));
        $searchParts = array_filter($searchParts, fn($item) => $item !== '');
        if (count($searchParts)) {
            foreach ($syntaxValues as $part => $values) {
                $items = array_filter($values, fn($value) => array_reduce($searchParts, fn($carry, $searchItem) => $carry && str_contains(strtolower($value['label']), $searchItem), true));
                $syntaxValues[$part] = array_values($items);
            }
        }
        // When no search string was given, we reduce the items per part to 100
        $omittedItems = false;

        foreach ($syntaxValues as $part => $values) {
            if (count($values) > 100) {
                $omittedItems = true;
                $syntaxValues[$part] = array_slice($values, 0, 100);
            }

        }

        return [
            'omittedItems' => $omittedItems,
            'syntax' => $syntaxValues,
            'pipe' => $pipeValues,
        ];
    }

    /**
     * Returns values for a syntax "[[]]" section.
     * @param string $part
     * @param array $cache ProcessVersion-Cache
     * @param $processVersionCacheArrays
     * @param $request
     * @param $actionType
     * @return array|array[]
     */
    private function getSyntaxPart(string $part, array $cache, $processVersionCacheArrays, $request, $actionType) {
        return match ($part) {
            // Graph Daten
            'process.meta' => $cache['syntax_values']['syntax']['process.meta'],
            'process.outputs' => $cache['syntax_values']['syntax']['process.outputs'],
            'process.artifacts' => $cache['syntax_values']['syntax']['process.artifacts'],
            'process.actions' => $cache['syntax_values']['syntax']['process.actions'],
            'process.status' => $cache['syntax_values']['syntax']['process.status'],
            'process.urls' => $cache['syntax_values']['syntax']['process.urls'],
            'public_apis' => $cache['syntax_values']['syntax']['public_apis'],
            'variables' => $cache['syntax_values']['syntax']['variables'],
            'graphs.urls' => $cache['syntax_values']['syntax']['graphs.urls'],
            // Daten zu den Verknüpfungs-Referenzen.
            'reference.metas' => (new SyntaxReferenceMetas($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'reference.relation_data' => (new SyntaxReferenceRelationData($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'reference.outputs' => (new SyntaxReferenceOutputs($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'reference.status' => (new SyntaxReferenceStatus($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'reference.urls' => (new SyntaxReferenceUrls($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),

            // Falls ein Aktionstyp-Kontext angegeben wurde, werden hier die Aktions-Syntax-Werte zurückgegen.
            'action.outputs' => $actionType ? (new SyntaxActionOutputs($actionType))->toArray($request) : (new SyntaxActionOutputsCollection($cache))->toArray($request),
            'action.processors' => $actionType ? (new SyntaxActionProcessors($actionType))->toArray($request) : (new SyntaxActionProcessorsCollection($cache))->toArray($request),
            'action.artifacts' => $actionType ? (new SyntaxActionArtifacts($actionType))->toArray($request) : (new SyntaxActionArtifactsCollection($cache))->toArray($request),

            // Meta-Daten zum aktuellen Graph und externe Graphen
            'graphs.meta' => (new SyntaxGraphsMeta($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),

            // Globale Syntax-Werte
            'url' => (new SyntaxUrl($cache))->toArray($request),
            'auth' => (new SyntaxAuth($cache))->toArray($request),
            'auth.identity.outputs' => (new SyntaxAuthIdentityOutputs($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'auth.identity.status' => (new SyntaxAuthIdentityStatus($cache))->additional(['processVersionCaches' => $processVersionCacheArrays])
                ->toArray($request),
            'system' => (new SyntaxSystem($cache))->toArray($request),
            'date' => (new SyntaxDate($cache))->toArray($request),
            'faker' => (new SyntaxFaker($cache))->toArray($request),
            default => []
        };
    }

    /**
     * Returns pipe syntax values.
     * @param string $part
     * @param array $cache
     * @param $processVersionCacheArrays
     * @param $request
     * @param $actionType
     * @return array|array[]
     */
    private function getPipePart(string $part, array $cache, $processVersionCacheArrays, $request, $actionType) {
        $allCaches = collect([$cache, ...$processVersionCacheArrays]);

        return match ($part) {
            'environment_groups' => $cache['syntax_values']['pipe']['environment_groups'],
            'environment_users' => $cache['syntax_values']['pipe']['environment_users'],
            'environment_processes' => $cache['syntax_values']['pipe']['environment_processes'],
            'environment_bots' => $cache['syntax_values']['pipe']['environment_bots'],
            'environment_public_apis' => $cache['syntax_values']['pipe']['environment_public_apis'],
            'environment_connectors' => $cache['syntax_values']['pipe']['environment_connectors'],
            'environment_requests' => $cache['syntax_values']['pipe']['environment_requests'],
            'environment_variables' => $cache['syntax_values']['pipe']['environment_variables'],
            'roles' => $cache['syntax_values']['pipe']['roles'],
            'events' => $cache['syntax_values']['pipe']['events'],
            'list_configs' => $cache['syntax_values']['pipe']['list_configs'],
            'relation_types' => $cache['syntax_values']['pipe']['relation_types'],
            'templates' => $cache['syntax_values']['pipe']['templates'],
            'action_types' => $cache['syntax_values']['pipe']['action_types'],

            // Falls ein Aktionstyp-Kontext angegeben wurde, werden hier die Aktions-Syntax-Werte zurückgegen.
            'action_type_processors' => $actionType ? (new PipeActionTypeProcessors($actionType))->toArray($request) : [],

            // Daten von externen Graphen.
            'graphs' => (new PipeGraphs($allCaches))->toArray($request),
            'graphs_action_types' => (new PipeGraphActionTypes($allCaches))->toArray($request),
            'graphs_relation_types' => (new PipeGraphRelationTypes($processVersionCacheArrays))->toArray($request),
            default => []
        };
    }
}
