<?php

namespace App\Http\Resources\SyntaxValues;

use App\Http\Resources\Cache\ProcessVersionCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class PipeGraph
 * @package App\Http\Resources
 */
class PipeGraphs extends JsonResource {

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
        $items = [];

        $this->resource->each(function ($processVersionCache) use (&$items) {
            $labelValue = $processVersionCache['process_version_simple']['namespace'] . '/' . $processVersionCache['process_version_simple']['identifier'];
            $items[] = (array)new Item($labelValue, $labelValue, 'graphs');
        });

        return $items;
    }
}
