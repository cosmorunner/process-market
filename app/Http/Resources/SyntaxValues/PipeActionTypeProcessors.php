<?php

namespace App\Http\Resources\SyntaxValues;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Processor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class PipeActionTypeProcessors
 * @package App\Http\Resources
 */
class PipeActionTypeProcessors extends JsonResource {

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
        return $this->resource->processors->map(function (Processor $processor) {
            if(Definition::validNamespace($processor->identifier)) {
                $processorName = explode('@', $processor->identifier)[0];
            } else {
                $processorName = Processor::names()[$processor->identifier];
            }

            $label = 'Prozessor-Ergebnis - ' . $processorName . ' - ID: ' . Str::limit($processor->id, 4, '');
            $value = 'processor|' . $processor->id;

            return new Item($label, $value, 'action_type_processors');
        })->toArray();
    }
}
