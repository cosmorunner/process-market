<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentGroups
 * @package App\Http\Resources
 */
class PipeEnvironmentGroups extends JsonResource {

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
        $groupAliases = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->groups)
            ->flatten()
            ->pluck('aliases')
            ->flatten()
            ->unique();

        return $groupAliases->map(function (string $alias) {
            return new Item('Gruppe - ' . $alias, 'app::group|' . $alias, 'environment_groups');
        })->toArray();
    }
}
