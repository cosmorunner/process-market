<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxUrl
 * Url-Werte
 * @package App\Http\Resources
 */
class SyntaxUrl extends JsonResource {

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
            (array) new Item('URL Parameter...', '[[url.query.NAME]]', 'url'),
            (array) new Item('URL Kontext-Parameter (Model-Pipe-Notation)', '[[url.query.context]]', 'url'),
            (array) new Item('URL Kontext-Parameter (Nur Id)', '[[url.query.context.key]]', 'url')
        ];
    }
}
