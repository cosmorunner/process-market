<?php

namespace App\Http\Resources\SyntaxValues;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxActionOutputsCollection
 * Aktionstyp-Output Daten
 * @package App\Http\Resources
 */
class SyntaxActionOutputsCollection extends JsonResource {

    /**
     * @var array ProcessVersionCache
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = [];

        foreach ($this->resource['process_version_simple']['action_types'] as $actionType) {
            foreach ($actionType['outputs'] as $output) {
                $items[] = (array)new Item('Aktions-Daten - ' . $output['name'], '[[action.outputs.' . $output['name'] . ']]', 'action.outputs');
            }
        }

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return array_values(array_unique($items, SORT_REGULAR));

    }
}
