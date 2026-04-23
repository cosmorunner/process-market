<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentUsers
 * @package App\Http\Resources
 */
class PipeEnvironmentUsers extends JsonResource {

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
        $userAliases = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->users)
            ->flatten()
            ->pluck('aliases')
            ->flatten()
            ->unique();

        return $userAliases->map(function (string $alias) {
            return new Item('Benutzer - ' . $alias, 'app::user|' . $alias, 'environment_users');
        })->toArray();
    }
}
