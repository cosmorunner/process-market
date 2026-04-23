<?php

namespace App\Http\Resources;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Output;
use App\ProcessType\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ProcessesSupportData
 * Gibt die Support-Data für eine Listenkonfiguration vom Template "processes" zurück.
 * @package App\Http\Resources
 */
class ProcessesSupportData extends BaseSupportData {

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
        $latestProcessVersions = $this->resource->pluck('latestPublishedVersion')->merge([$this->processVersion]);

        /* @var DefinitionAndElements[] $definitions */
        $statusTypes = [];
        $outputs = [];

        foreach ($latestProcessVersions as $latestProcessVersion) {
            $definition = $latestProcessVersion->definition;
            $fullNamespace = $definition->namespace . '/' . $definition->identifier;
            $statusTypes[$fullNamespace] = $this->statusTypeSupportData($definition->statusTypes);
            $outputs[$fullNamespace] = $definition->outputs->map(fn(Output $output) => [
                'column' => 'processes.data->' . $output->name,
                'alias' => 'processes_data_' . $output->name,
                'label' => 'Prozess-Daten - ' . $output->name
            ])->unique('alias')->toArray();
        }

        $allTableColumns = $this->getCoreTableColumns();

        foreach ($statusTypes as $statusTypeColumns) {
            $allTableColumns = array_merge($allTableColumns, $statusTypeColumns);
        }
        foreach ($outputs as $outputColumns) {
            $allTableColumns = array_merge($allTableColumns, $outputColumns);
        }

        $processRoles = [];
        $processActions = [];

        foreach ($latestProcessVersions as $processVersion) {
            $namespace = $processVersion->definition->namespace;
            $identifier = $processVersion->definition->identifier;
            $processRoles[$namespace . '/' . $identifier] = $processVersion->definition->roles->map(fn(Role $role) => [
                'name' => $role->name,
                'id' => $role->id
            ]);
            $processActions[$namespace . '/' . $identifier] = $processVersion->definition->actionTypes->map(fn(ActionType $actionType) => [
                'name' => $actionType->name,
                'id' => $actionType->id,
                'reference' => $actionType->reference
            ]);
        }

        return [
            'coreTableColumns' => $this->getCoreTableColumns(['processes', 'process_type_metas', 'process_types']),
            'processes' => (new ProcessCollection($this->resource))->toArray(request()),
            'processRoles' => $processRoles,
            'processActions' => $processActions,
            'statusTypes' => $statusTypes,
            'outputs' => $outputs,
            'allColumns' => $allTableColumns,
            'systemUrls' => $this->getSystemUrls(),
            'processUrls' => $this->getProcessUrls($latestProcessVersions, true),
            'definitions' => SimpleDefinition::collection($latestProcessVersions)->toArray(request()),
            'select' => $this->getSelectItems()
        ];
    }

}
