<?php

namespace App\Http\Resources;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Definition
 * @package App\Http\Resources
 */
class DefinitionAndElements extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'definition' => $this->resource->definition->toArray(),
            'elements' => $this->resource->calculated
        ];
    }
}
