<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Tag
 * @package App\Http\Resources
 */
class Tag extends JsonResource {

    /**
     * @var \App\Models\Tag
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'name' => $this->resource->name,
            'color' => $this->resource->color,
        ];
    }
}
