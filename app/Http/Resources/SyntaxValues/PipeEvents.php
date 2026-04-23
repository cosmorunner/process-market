<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEvents
 * @package App\Http\Resources
 */
class PipeEvents extends JsonResource {

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
        return $this->resource->definition->events->map(function (Event $event) {
            return new Item('Event - ' . $event->name, 'event|' . $event->id, 'events');
        })->toArray();
    }
}
