<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SolutionVersions
 * @package App\Http\Resources
 */
class SolutionVersions extends JsonResource {

    /**
     * @var \App\Models\Solution
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return $this->resource->publishedVersions()->pluck('version')->toArray();
    }
}
