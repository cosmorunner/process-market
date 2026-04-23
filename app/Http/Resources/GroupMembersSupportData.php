<?php

namespace App\Http\Resources;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class GroupMembersSupportData
 * Returns the support data for the group member template.
 * @package App\Http\Resources
 */
class GroupMembersSupportData extends BaseSupportData {

    /**
     * @var Collection|Process[]
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array {
        $allColumns = config('list_config.template_columns.group_members');

        return [
            'allColumns' => config('list_config.template_columns.group_members'),
            'select' => collect($allColumns)->map(fn($item) => [
                'column' => $item['column'],
                'alias' => $item['alias']
            ]),
        ];
    }

}
