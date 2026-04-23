<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Organisation
 * @package App\Http\Resources
 */
class Organisation extends JsonResource {

    /**
     * @var \App\Models\Organisation
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
            'namespace' => $this->resource->namespace
        ];
    }
}
