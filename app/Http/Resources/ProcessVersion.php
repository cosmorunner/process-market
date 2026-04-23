<?php

namespace App\Http\Resources;

use App\Graph\Cytoscape;
use App\Models\ProcessVersion as ProcessVersionModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

/**
 * Class Process
 * @package App\Http\Resources
 */
class ProcessVersion extends JsonResource {

    /**
     * @var ProcessVersionModel
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function toArray($request) {
        return [
            'id' => $this->resource->id,
            'definition' => $this->resource->definition->toArray(),
            'calculated' => Cytoscape::renderHTMLElements($this->resource->definition, $this->resource->calculated),
            'demo_data' => $this->resource->demo_data,
            'version' => $this->resource->version,
            'published_at' => $this->resource->published_at,
        ];
    }
}
