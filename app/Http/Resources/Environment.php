<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Environment
 * @package App\Http\Resources
 */
class Environment extends JsonResource {

    /**
     * @var \App\Models\Environment
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
            'process_version_id' => $this->resource->process_version_id,
            'default' => $this->resource->default,
            'default_user' => $this->resource->default_user,
            'public' => $this->resource->public,
            'initial_action_type_id' => $this->resource->initial_action_type_id,
            'query_context' => $this->resource->query_context,
            'blueprint' => $this->resource->getRawBlueprint(),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at
        ];
    }
}
