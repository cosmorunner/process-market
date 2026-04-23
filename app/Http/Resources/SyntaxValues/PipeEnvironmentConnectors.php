<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentConnectors
 * @package App\Http\Resources
 */
class PipeEnvironmentConnectors extends JsonResource {

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
        $environments = $this->resource->environments;
        $identifiers = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->connectors)
            ->flatten()
            ->pluck('identifier')
            ->flatten()
            ->unique();

        return $identifiers->map(function (string $identifier) {
            return new Item('Connector - ' . $identifier, 'app::connector|' . $identifier, 'environment_connectors');
        })->toArray();
    }
}
