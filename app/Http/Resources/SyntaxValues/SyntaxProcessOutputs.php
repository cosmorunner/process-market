<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\Output;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxProcessOutputs
 * Process-Output Daten
 * @package App\Http\Resources
 */
class SyntaxProcessOutputs extends JsonResource {

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
        $items = $this->resource->definition->outputs->map(function (Output $output) {
            return new Item('Prozess-Daten - ' . $output->name, '[[process.outputs.' . $output->name . ']]', 'process.outputs');
        })->toArray();

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return $items;
    }
}
