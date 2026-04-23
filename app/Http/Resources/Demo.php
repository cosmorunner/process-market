<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Demo
 * @package App\Http\Resources
 */
class Demo extends JsonResource {

    /**
     * @var \App\Models\Simulation
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
            'finished_at' => $this->resource->finished_at,
            'allisa_user_id' => $this->resource->allisa_user_id,
            'connector_error' => $this->additional['error'] ?? null,
            'connector_error_message' => $this->additional['error_message'] ?? null
        ];
    }
}
