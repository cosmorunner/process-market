<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class License
 * @package App\Http\Resources
 */
class License extends JsonResource {

    /**
     * @var \App\Models\License
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
            'resource_namespace' => $this->resource->resource->namespace,
            'resource_identifier' => $this->resource->resource->identifier,
            'resource_full_namespace' => $this->resource->resource->full_namespace,
            'owner_id' => $this->resource->owner_id,
            'owner_namespace' => $this->resource->owner->namespace,
            'owner_name' => $this->resource->owner->name,
            'level' => $this->resource->level,
            'created_at' => $this->resource->created_at->toDateTimeString()
        ];
    }
}
