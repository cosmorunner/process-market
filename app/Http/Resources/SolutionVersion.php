<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SolutionVersion
 * @package App\Http\Resources
 */
class SolutionVersion extends JsonResource {

    /**
     * @var \App\Models\SolutionVersion
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
            'solution_id' => $this->resource->solution_id,
            'version' => $this->resource->version,
            'changelog' => $this->resource->changelog,
            'complexity_score' => $this->resource->complexity_score,
            'published_at' => $this->resource->published_at->toDateTimeString(),
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'processes' => Process::collection($this->resource->processes())->toArray($request)
        ];
    }
}
