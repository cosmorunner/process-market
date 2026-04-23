<?php

namespace App\Http\Resources\Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CachedModel
 * @package App\Http\Resources
 */
class ModelCache extends JsonResource {

    /**
     * @var Model
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [];
    }
}
