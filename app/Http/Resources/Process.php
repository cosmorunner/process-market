<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Process
 * @package App\Http\Resources
 */
class Process extends JsonResource {

    /**
     * @var \App\Models\Process
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'namespace' => $this->resource->namespace,
            'identifier' => $this->resource->identifier,
            'author_image_path' => $this->resource->author->imagePath(),
            'full_namespace' => $this->resource->namespace . '/' . $this->resource->identifier,
            'author_name' => $this->resource->author->name,
            'latest_version' => $this->resource->latest_version,
            'visibility' => $this->resource->visibility,
            'license_options' => LicenseOption::collection($this->resource->license_options)->toArray($request),
        ];
    }
}
