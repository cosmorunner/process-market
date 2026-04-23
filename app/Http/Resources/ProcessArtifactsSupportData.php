<?php

namespace App\Http\Resources;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ProcessArtifactsSupportData
 * Returns the support data for a list configuration from the "process_artifacts" template.
 * @package App\Http\Resources
 */
class ProcessArtifactsSupportData extends BaseSupportData {

    /**
     * @var Collection|Process[]
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $coreTableColumns = [
            ...$this->getCoreTableColumns(['actions', 'documents', 'artifacts', 'users'])
        ];
        $allTableColumns = $coreTableColumns;

        return [
            'coreTableColumns' => $coreTableColumns,
            'allColumns' => $allTableColumns,
            'documentTypes' => [
                ['value' => 'App\Models\UploadedFile', 'label' => 'Hochgeladene Datei'],
                ['value' => 'App\Models\DownloadedFile', 'label' => 'Heruntergeladene Datei'],
                ['value' => 'App\Models\SystemDocument', 'label' => 'System-Dokument'],
                ['value' => 'App\Models\GeneratedPdf', 'label' => 'Erstelltes Pdf']
            ],
            'select' => $this->getSelectItems()
        ];
    }

}
