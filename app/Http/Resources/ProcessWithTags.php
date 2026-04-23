<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProcessWithTags
 * @package App\Http\Resources
 */
class ProcessWithTags extends JsonResource {

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
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'namespace' => $this->resource->namespace,
            'identifier' => $this->resource->identifier,
            'full_namespace' => $this->resource->namespace . '/' . $this->resource->identifier,
            'author_name' => $this->resource->author->name,
            'latest_version' => $this->resource->latest_version,
            'visibility' => $this->resource->visibility,
            'license_options' => LicenseOption::collection($this->resource->license_options)->toArray($request),
            'tags' => $this->resource->tags->map(fn(Tag $tag) => $tag->name)
        ];
    }
}
