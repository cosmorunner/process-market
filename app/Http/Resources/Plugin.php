<?php

namespace App\Http\Resources;

use App\Models\Plugin as PluginModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Plugin
 * @package App\Http\Resources
 */
class Plugin extends JsonResource {

    /**
     * The "data" wrapper that should be applied.
     * @var string|null
     */
    public static $wrap = null;

    /**
     * @var PluginModel
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'name' => $this->resource->name,
            'full_namespace' => $this->resource->full_namespace,
            'latest_version' => $this->resource->latest_version,
            'full_namespace_with_version' => $this->resource->full_namespace . '@' . $this->resource->latest_version,
            'data' => array_merge($this->resource->data, $this->resource->latestPublishedVersion->data)
        ];
    }
}
