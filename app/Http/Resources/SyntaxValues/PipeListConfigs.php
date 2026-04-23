<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\ListConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeListConfigs
 * @package App\Http\Resources
 */
class PipeListConfigs extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return $this->resource->definition->listConfigs->map(function (ListConfig $listConfig) {
            return new Item('Liste - ' . $listConfig->name . ' - ' . $listConfig->slug, 'listConfig|' . $listConfig->slug, 'list_configs');
        })->toArray();
    }
}
