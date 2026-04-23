<?php

namespace App\Http\Resources;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Eine Prozess-Version mit dem Namen und der Beschreibung des dazugehörigen Prozesses.
 * Class ProcessVersionMeta
 * @package App\Http\Resources
 */
class ProcessVersionMeta extends JsonResource {

    /**
     * @var ProcessVersion
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
            'title' => $this->resource->process->title,
            'description' => $this->resource->process->description,
            'namespace' => $this->resource->process->namespace,
            'identifier' => $this->resource->process->identifier,
            'version' => $this->resource->version,
            'full_namespace' => $this->resource->process->namespace . '/' . $this->resource->process->identifier . '@' . $this->resource->version,
        ];
    }
}
