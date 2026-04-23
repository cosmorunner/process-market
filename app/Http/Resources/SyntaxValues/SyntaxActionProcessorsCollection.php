<?php

namespace App\Http\Resources\SyntaxValues;

use App\ProcessType\Processor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class SyntaxActionProcessorsCollection
 * Aktionstyp-Prozessor Daten
 * @package App\Http\Resources
 */
class SyntaxActionProcessorsCollection extends JsonResource {

    /**
     * @var array GraphCache
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
            foreach ($actionType['processors'] as $processor) {
                if($processor['identifier'] === 'create_process') {
                    $label = 'Prozessor-Ergebnis - ' . Processor::names()[$processor['identifier']] . ' - ID: ' . Str::limit($processor['id'], 4, '') . ' - Prozess-Id';
                    $value = '[[action.processors.' . $processor['id'] . '.results(type=process).id]]';
                    $items[] = (array)new Item($label, $value, 'action.processors');
                }
            }
        }

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return array_values(array_unique($items, SORT_REGULAR));

    }
}
