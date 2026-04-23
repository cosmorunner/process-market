<?php

namespace App\Http\Resources\SyntaxValues;

use App\Http\Resources\Cache\ProcessVersionCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class PipeGraphRelationTypes
 * @package App\Http\Resources
 */
class PipeGraphRelationTypes extends JsonResource {

    /**
     * @var Collection<ProcessVersionCache>
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = collect();

        $this->resource->each(function ($processVersionCache) use (&$items) {
            $latestNamespace = $processVersionCache['process_version_simple']['namespace'] . '/' . $processVersionCache['process_version_simple']['identifier'];

            foreach ($processVersionCache['process_version_simple']['relation_types'] as $relationType) {
                // Latest
                $label = $latestNamespace . ' - Verknüpfungstyp - ' . $relationType['name'];
                $value = $latestNamespace . '::relationType|' . $relationType['reference'];
                $items->push((array) new Item($label, $value, 'graphs_relation_types'));
            }
        });

        return $items->unique('value')->values()->toArray();
    }
}
