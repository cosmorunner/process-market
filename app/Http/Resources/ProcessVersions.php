<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProcessVersions
 * @package App\Http\Resources
 */
class ProcessVersions extends JsonResource {

    /**
     * @var \App\Models\Process
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
