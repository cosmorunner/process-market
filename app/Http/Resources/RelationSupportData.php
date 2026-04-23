<?php

namespace App\Http\Resources;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class RelationsSupportData
 * Gibt die Support-Data für die Relationen des Prozesstyps (Definition) zurück.
 * @package App\Http\Resources
 */
class RelationSupportData extends BaseSupportData {

    /**
     * @var Collection|Process[]
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $attributes = $this->getAllRelationAttributes();

        return [
            'allColumns' => $this->getAllRelationAttributes(),
            'select' => collect($attributes)->map(fn($item) => [
                'column' => $item['column'],
                'alias' => $item['alias']
            ]),
        ];
    }

    /**
     * Gibt die möglichen Attribute aller Contexts aller Relations zurück.
     * @return array
     */
    public function getAllRelationAttributes() {
        $attributes = collect(config('list_config.relations'));

        return [...$attributes->flatten(2)->values()];
    }
}
