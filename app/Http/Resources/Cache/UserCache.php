<?php

namespace App\Http\Resources\Cache;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class CachedUser
 * @package App\Http\Resources
 */
class UserCache extends ModelCache {

    /**
     * @var User
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $processVersions = $this->resource->latestPublishedProcessVersions();

        /* @var Organisation $organisation */
        foreach ($this->resource->organisations as $organisation) {
            /** @noinspection PhpParamsInspection */
            $processVersions = $processVersions->concat($organisation->latestPublishedProcessVersions());
        }

        $latestLicensedProcessVersions = $this->resource->latestLicencedProcessVersions();

        return [
            'published_process_version_ids' => $processVersions->pluck('id')->toArray(),
            'licenses_process_version_ids' => $latestLicensedProcessVersions->pluck('id')->unique()->toArray()
        ];
    }
}
