<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeRoles
 * @package App\Http\Resources
 */
class PipeRoles extends JsonResource {

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
        return $this->resource->definition->roles->map(function (Role $role) {
            return new Item('Rolle - ' . $role->name, 'role|' . $role->id, 'roles');
        })->toArray();
    }
}
