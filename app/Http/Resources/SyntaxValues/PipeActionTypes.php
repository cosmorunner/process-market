<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeActionTypes
 * @package App\Http\Resources
 */
class PipeActionTypes extends JsonResource {

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
        return $this->resource->definition->actionTypes->map(function (ActionType $actionType) {
            return new Item('Aktion - ' . $actionType->name, 'actionType|' . $actionType->id, 'action_types');
        })->toArray();
    }
}
