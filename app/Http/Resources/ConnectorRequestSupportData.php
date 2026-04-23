<?php

namespace App\Http\Resources;

use App\Models\Process;
use App\Traits\UsesAliasString;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ConnectorRequestSupportData
 * Returns the support data for a list configuration from the ‘connector_request’ template.
 * @package App\Http\Resources
 */
class ConnectorRequestSupportData extends BaseSupportData {

    use UsesAliasString;

    /**
     * @var Collection|Process[]
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'select' => $this->getSelectItems()
        ];
    }

}
