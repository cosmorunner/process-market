<?php

namespace App\Http\Resources;

use App\Models\Process;
use App\Traits\UsesAliasString;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ProcessDocumentsSupportData
 * Gibt die Support-Data für eine Listenkonfiguration vom Template "process_documents" zurück.
 * @package App\Http\Resources
 */
class ProcessDocumentsSupportData extends BaseSupportData {

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
        $metaColumns = $this->getCoreTableColumns(['processes', 'process_type_metas', 'process_types', 'documents']);

        return [
            'metaColumns' => $metaColumns,
            'allColumns' => $metaColumns,
            'select' => $this->getSelectItems()
        ];
    }

}
