<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxSystem
 * System-Metadaten
 * @package App\Http\Resources
 */
class SyntaxSystem extends JsonResource {

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
            (array) new Item('System-Name', '[[system.meta.name]]', 'system'),
            (array) new Item('System-Beschreibung', '[[system.meta.description]]', 'system'),
            (array) new Item('System-URL', '[[system.meta.url]]', 'system'),
            (array) new Item('System-Logo-URL', '[[system.meta.logo_url]]', 'system'),
        ];
    }
}
