<?php

namespace App\Http\Resources;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class GroupsSupportData
 * Returns the support data for the "groups" list template
 * @package App\Http\Resources
 */
class GroupsSupportData extends BaseSupportData {

    /**
     * @var Collection|Process[]
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array {
        $groupAttributes = [...collect(config('list_config.columns.groups'))->values()];
        $baseSelectItems = collect($groupAttributes)->map(fn($item) => [
            'column' => $item['column'],
            'alias' => $item['alias']
        ])->toArray();

        return [
            'allColumns' => $groupAttributes,
            'select' => array_merge($this->getSelectItems(), $baseSelectItems)
        ];
    }

}
