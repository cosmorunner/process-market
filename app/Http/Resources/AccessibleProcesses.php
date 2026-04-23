<?php

namespace App\Http\Resources;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AccessibleProcesses
 * @package App\Http\Resources
 */
class AccessibleProcesses extends JsonResource {

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
        $userProcesses = $this->resource->publishedProcesses();
        $licensesProcesses = $this->resource->openSourceProcessLicenses()->get()->pluck('resource');

        $organisationProcesses = $this->resource->organisations->mapWithKeys(function(Organisation $organisation) {
            $licensesProcesses = $organisation->openSourceProcessLicenses()->get()->pluck('resource');

            return [
                $organisation->namespace => [
                    'processes' => Process::collection($organisation->publishedProcesses())->toArray(request()),
                    'licensesProcesses' => Process::collection($licensesProcesses)->toArray(request())
                ]
            ];
        });

        return [
            'processes' => Process::collection($userProcesses)->toArray(request()),
            'licensesProcesses' => Process::collection($licensesProcesses)->toArray(request()),
            'organisations' => [...$organisationProcesses]
        ];
    }
}
