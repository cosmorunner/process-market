<?php

namespace App\Http\Resources\SyntaxValues;

use App\Http\Resources\Cache\ProcessVersionCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class PipeGraphActionTypes
 * @package App\Http\Resources
 */
class PipeGraphActionTypes extends JsonResource {

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
            $fullNamespace = $processVersionCache['process_version_simple']['full_namespace'];
            $latestNamespace = $processVersionCache['process_version_simple']['namespace'] . '/' . $processVersionCache['process_version_simple']['identifier'] . '@latest';

            foreach ($processVersionCache['process_version_simple']['action_types'] as $actionType) {
                $label = $latestNamespace . ' - Aktion - ' . $actionType['name'];
                $value = $latestNamespace . '::actionType|' . $actionType['id'];
                $items[] = (array)new Item($label, $value, 'graphs_action_types');

                $label = $fullNamespace . ' - Aktion - ' . $actionType['name'];
                $value = $fullNamespace . '::actionType|' . $actionType['id'];
                $items[] = (array)new Item($label, $value, 'graphs_action_types');
            }
        });

        return $items;
    }
}
