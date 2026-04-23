<?php

namespace App\Http\Resources;

use App\Loaders\PipeLoader;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Output;
use App\ProcessType\RelationType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ProcessesSupportData.
 * Gibt die Support-Data für eine Listenkonfiguration vom Template "processes" zurück.
 * @package App\Http\Resources
 */
class ProcessRelationsSupportData extends BaseSupportData {

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
        $relationTypeData = [];
        $relationTypePipeNotations = [];

        foreach ($latestProcessVersions as $latestProcessVersion) {
            $definition = $latestProcessVersion->definition;
            $fullNamespace = $definition->namespace . '/' . $definition->identifier;

            // Status-Spalten
            $statusTypes[$fullNamespace] = $this->statusTypeSupportData($definition->statusTypes);

            // Output-Spalten
            if ($definition->outputs->isNotEmpty()) {
                $outputs[$fullNamespace] = $definition->outputs->map(fn(Output $output) => [
                    'column' => 'processes.data->' . $output->name,
                    'alias' => 'processes_data_' . $output->name,
                    'label' => 'Prozess-Daten - ' . $output->name
                ])->toArray();
            }
        }

        foreach ($this->resource as $process) {
            $items = collect();
            $namespace = $process->namespace . '/' . $process->identifier;
            $processVersions = $process->versions()->where('published_at', '!=', null)->latest('published_at')->take(10)->get();

            /* @var ProcessVersion $processVersion */
            foreach ($processVersions as $processVersion) {
                /** @noinspection PhpParamsInspection */
                $items = $items->concat(collect($processVersion->definition->relationTypes));
            }

            $relationTypePipeNotations[$namespace] = $items->unique('reference')
                ->map(fn(RelationType $relationType) => PipeLoader::toString($relationType, $namespace . ' - ' . $relationType->name, '', true, 'reference'))
                ->toArray();

            $grouped = $items->groupBy('reference');
            $relationTypeData[$namespace] = $grouped->mapWithKeys(function (Collection $relationTypes, $key) {
                return [
                    $key => $relationTypes->flatMap(function ($values) {
                        return $values->default;
                    })->keys()->unique()->map(fn($key) => [
                        'column' => 'relations.data->' . $key,
                        'alias' => 'relations_data_' . $key,
                        'label' => $key
                    ])->sortBy('label')->toArray()
                ];
            });
        }

        // Additionally add relation data from the current process
        $definition = $this->processVersion->definition;
        $relationTypes = $definition->relationTypes;

        if ($relationTypes->isNotEmpty()) {
            // Verknüpfungstypen PipeLoader-Syntaxen
            $relationTypePipeNotations[$definition->fullNamespace()] = collect($definition->relationTypes)
                ->map(fn(RelationType $relationType) => PipeLoader::toString($relationType, $relationType->name, '', true, 'reference', true))
                ->unique()
                ->toArray();

            // Zusätzlich noch Verknüpfungsdaten vom aktuellen Prozess hinzufügen
            $relationTypeData[$definition->fullNamespace()] = collect($relationTypes)->mapWithKeys(function (RelationType $relationType) {
                return [
                    $relationType->reference => collect($relationType->default)->keys()->map(fn($key) => [
                        'column' => 'relations.data->' . $key,
                        'alias' => 'relations_data_' . $key,
                        'label' => $key
                    ])->toArray()
                ];
            });
        }

        $coreTableColumns = $this->getCoreTableColumns(['processes', 'process_type_metas', 'process_types', 'relations']);
        $allTableColumns = $coreTableColumns;

        foreach ($statusTypes as $statusTypeColumns) {
            $allTableColumns = array_merge($allTableColumns, $statusTypeColumns);
        }

        foreach ($outputs as $outputColumns) {
            $allTableColumns = array_merge($allTableColumns, $outputColumns);
        }

        foreach ($relationTypeData as $relationTypes) {
            foreach ($relationTypes as $relationTypeColumns) {
                $allTableColumns = array_merge($allTableColumns, $relationTypeColumns);
            }
        }

        return [
            'coreTableColumns' => $coreTableColumns,
            'processes' => (new ProcessCollection($this->resource))->toArray(request()),
            'statusTypes' => $statusTypes,
            'relationTypesPipeNotation' => $relationTypePipeNotations,
            'relationTypeData' => $relationTypeData,
            'outputs' => $outputs,
            'systemUrls' => $this->getSystemUrls(),
            'processUrls' => $this->getProcessUrls($latestProcessVersions, true),
            'allColumns' => $allTableColumns,
            'select' => $this->getSelectItems()
        ];
    }

}
