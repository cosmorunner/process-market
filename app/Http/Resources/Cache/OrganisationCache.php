<?php /** @noinspection PhpUnused */

namespace App\Http\Resources\Cache;

use App\Models\Organisation;
use Illuminate\Http\Request;

/**
 * Class OrganisationCache
 * @package App\Http\Resources
 */
class OrganisationCache extends ModelCache {

    /**
     * @var Organisation
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $latestProcessVersions = $this->resource->latestPublishedProcessVersions();
        $latestLicensedProcessVersions = $this->resource->latestLicencedProcessVersions();

        return [
            'published_process_version_ids' => $latestProcessVersions->pluck('id')->toArray(),
            'licenses_process_version_ids' => $latestLicensedProcessVersions->pluck('id')->unique()->toArray()
        ];
    }
}
