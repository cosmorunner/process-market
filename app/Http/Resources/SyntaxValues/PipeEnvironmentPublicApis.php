<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentPublicApis
 * @package App\Http\Resources
 */
class PipeEnvironmentPublicApis extends JsonResource {

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
        $slugs = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->publicApis)
            ->flatten()
            ->pluck('slug')
            ->unique();

        return $slugs->map(function (string $slug) {
            return new Item('Öffentl. API - ' . $slug, 'app::publicApi|' . $slug, 'environment_public_apis');
        })->toArray();
    }
}
