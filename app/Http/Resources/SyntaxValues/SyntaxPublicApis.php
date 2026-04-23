<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Environment\PublicApi;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxPublicApis
 * Daten der öffentlichen URLs.
 * @package App\Http\Resources
 */
class SyntaxPublicApis extends JsonResource {

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
        $publicApis = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->publicApis)
            ->flatten()
            ->unique('slug');

        return $publicApis->map(function (PublicApi $publicApi) {
            return new Item('Öffentl. API - URL - ' . $publicApi->slug, '[[system.public_apis.' . $publicApi->slug . '.url]]', 'public_apis');
        })->toArray();
    }
}
