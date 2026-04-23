<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentBots
 * @package App\Http\Resources
 */
class PipeEnvironmentBots extends JsonResource {

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
        $botAliases = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->bots)
            ->flatten()
            ->pluck('aliases')
            ->flatten()
            ->unique();

        return $botAliases->map(function (string $alias) {
            return new Item('Bot - ' . $alias, 'app::bot|' . $alias, 'environment_bots');
        })->toArray();
    }
}
