<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\RelationType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeRelationTypes
 * @package App\Http\Resources
 */
class PipeRelationTypes extends JsonResource {

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
        $items = [];

        $this->resource->definition->relationTypes->each(function (RelationType $relationType) use (&$items) {
            $value = 'relationType|' . $relationType->reference;
            $label = 'Verknüpfungstyp - ' . $relationType->name;
            $items[] = (array) new Item($label, $value, 'relation_types');
        });

        return $items;
    }
}
