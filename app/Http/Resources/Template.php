<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Template
 * @package App\Http\Resources
 */
class Template extends JsonResource {

    /**
     * @var \App\Models\Template
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
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'preview' => url($this->resource->preview),
            'mapping' => $this->resource->mapping,
            'type' => $this->resource->type
        ];
    }
}
