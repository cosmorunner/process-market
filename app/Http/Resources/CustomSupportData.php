<?php

namespace App\Http\Resources;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Output;
use App\Traits\UsesAliasString;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class CustomSupportData
 * Gibt die Support-Data für eine Listenkonfiguration vom Template "custom" zurück.
 * @package App\Http\Resources
 */
class CustomSupportData extends BaseSupportData {

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
        /* @var Collection|ProcessVersion[] $latestProcessVersions */
        $latestProcessVersions = $this->resource->map(fn(Process $process) => $process->latestVersion);

        /* @var DefinitionAndElements[] $definitions */
        $statusTypes = [];
        $outputs = [];

        foreach ($latestProcessVersions as $latestProcessVersion) {
            $definition = $latestProcessVersion->definition;
            $fullNamespace = $definition->namespace . '/' . $definition->identifier;
            $statusTypes[$fullNamespace] = $this->statusTypeSupportData($definition->statusTypes);
            $outputs[$fullNamespace] = $definition->outputs->map(fn(Output $output) => [
                'column' => 'processes.data->' . $output->name,
                'alias' => $output->name,
                'label' => $output->name
            ])->toArray();
        }

        $allTableColumns = $this->getCoreTableColumns();

        foreach ($statusTypes as $statusTypeColumns) {
            $allTableColumns = array_merge($allTableColumns, $statusTypeColumns);
        }
        foreach ($outputs as $outputColumns) {
            $allTableColumns = array_merge($allTableColumns, $outputColumns);
        }

        return [
            'tables' => $this->getTables(),
            'system_urls' => $this->getSystemUrls(),
            'process_urls' => $this->getProcessUrls($latestProcessVersions, true),
            'allColumns' => $allTableColumns,
            'select' => $this->getSelectItems()
        ];
    }

}
