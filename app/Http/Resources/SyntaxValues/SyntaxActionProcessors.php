<?php

namespace App\Http\Resources\SyntaxValues;

use App\ProcessType\ActionType;
use App\ProcessType\Processor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class SyntaxActionProcessors
 * Aktionstyp-Prozessoren Daten
 * @package App\Http\Resources
 */
class SyntaxActionProcessors extends JsonResource {

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

        $this->resource->processors->each(function (Processor $processor) use (&$items) {
            if ($processor->identifier === 'create_process') {
                $label = 'Prozessor-Ergebnis - ' . Processor::names()[$processor->identifier] . ' - ID: ' . Str::limit($processor->id, 4, '') . ' - Prozess-Id';
                $value = '[[action.processors.' . $processor->id . '.results(type=process).id]]';

                $items[] = (array)new Item($label, $value, 'action.processors');
            }
        })->toArray();

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return $items;
    }
}
