<?php

namespace App\Http\Resources;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SimpleDefinition
 * @package App\Http\Resources
 */
class SimpleDefinition extends JsonResource {

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
            'id' => $this->resource->id,
            'full_namespace' => $this->resource->full_namespace,
            'status_types' => $this->resource->definition->statusTypes->toArray()
        ];
    }
}
