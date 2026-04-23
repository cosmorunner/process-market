<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Alle Namespaces die der Benutzer nutzen kann um einen neuen Prozess hinzuzufügen.
 * Class UserNamespaces
 * @package App\Http\Resources
 */
class UserNamespaces extends JsonResource {

    /**
     * @var User
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'user' => $this->resource->namespace,
            'organisations' => $this->resource->organisations->pluck('namespace')
        ];
    }
}
