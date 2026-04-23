<?php

namespace App\Http\Resources\Cache;

use App\Http\Resources\ProcessVersionSimple;
use App\Http\Resources\SyntaxValues\ProcessVersionSyntaxValues;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;

/**
 * Class ProcessVersionCache
 * @package App\Http\Resources
 */
class ProcessVersionCache extends ModelCache {

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
        return [
            'syntax_values' => (new ProcessVersionSyntaxValues($this->resource))->toArray($request),
            'process_version_simple' => (new ProcessVersionSimple($this->resource))->toArray($request)
        ];
    }
}
