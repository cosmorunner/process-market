<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\Template;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeTemplates
 * @package App\Http\Resources
 */
class PipeTemplates extends JsonResource {

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
        return $this->resource->definition->templates->map(function (Template $template) {
            return new Item('Vorlage - ' . $template->name, 'template|' . $template->id, 'templates');
        })->toArray();
    }
}
