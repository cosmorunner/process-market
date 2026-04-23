<?php

namespace App\Http\Resources\SyntaxValues;

use App\ProcessType\ActionType;
use App\ProcessType\Output;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxActionOutputs
 * Aktionstyp-Output Daten
 * @package App\Http\Resources
 */
class SyntaxActionOutputs extends JsonResource {

    /**
     * @var ActionType
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = [];

        $this->resource->outputs->each(function (Output $output) use (&$items) {
            $items[] = (array)new Item('Aktions-Daten - ' . $output->name, '[[action.outputs.' . $output->name . ']]', 'action.outputs');
        })->toArray();

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return $items;
    }
}
