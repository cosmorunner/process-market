<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Simulation
 * @package App\Http\Resources
 */
class Simulation extends JsonResource {

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
        $process = $this->additional['process'] ?? [];
        $action = $this->additional['action'] ?? [];

        $data = [
            'id' => $this->resource->id,
            'process_id' => $this->resource->process_id,
            'allisa_id' => $process['id'] ?? null,
            'situation' => $process['situation'] ?? [],
            'categories' => $process['action_categories'] ?? [],
            'meta_data' => [
                'name' => $process['name'] ?? '',
                'description' => $process['description'] ?? '',
                'tags' => $process['tags'] ?? '',
                'image' => $process['image'] ?? '',
                'reference' => $process['reference'] ?? '',
            ],
            'data' => $process['data'] ?? [],
            'history' => $process['history'] ?? [],
            'accesses' => $process['accesses'] ?? [],
            'finished_at' => $this->resource->finished_at,
            'environment_id' => $this->resource->environment_id,
            'allisa_user_id' => $this->resource->allisa_user_id,
            'user_id' => $this->resource->user_id,
            'connector_error' => $this->additional['error'] ?? null,
            'connector_error_message' => $this->additional['error_message'] ?? null
        ];

        if ($action) {
            $data = array_merge($data, [
                'last_action' => $action
            ]);
        }

        return $data;
    }
}
