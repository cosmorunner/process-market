<?php

namespace App\Http\Resources\SyntaxValues;

use App\Http\Resources\Cache\ProcessVersionCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxReferenceOutputs
 * Prozessdaten der referenzierten Prozesse.
 * @package App\Http\Resources
 */
class SyntaxReferenceOutputs extends JsonResource {

    /**
     * @var ProcessVersionCache
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = [];
        $processVersionCaches = $this->additional['processVersionCaches'] ?? collect();
        $outputNames = collect($processVersionCaches)
            ->map(fn($processVersionCache) => $processVersionCache['process_version_simple']['outputs'])
            ->flatten(1)
            ->map(fn($output) => $output['name'])
            ->unique()
            ->toArray();

        // Internal relationtypes.
        foreach ($this->resource['process_version_simple']['relation_types'] as $relationType) {
            if (!in_array($relationType['connection_type'], ['n-1', '1-1'])) {
                continue;
            }

            foreach ($outputNames as $outputName) {
                $label = '[' . $relationType['reference'] . '] - Prozess-Daten - ' . $outputName;
                $value = '[[process.references.' . $relationType['reference'] . '.outputs.' . $outputName . ']]';
                $items[] = (array) new Item($label, $value, 'reference.outputs');
            }
        }

        // External relationtypes
        foreach ($processVersionCaches as $processVersionCache) {
            foreach ($processVersionCache['process_version_simple']['relation_types'] as $relationType) {
                // Here we switch the "n-1" to "1-n" because when the syntax ist used, it refers to the external relationtype.
                if (!in_array($relationType['connection_type'], ['1-n', '1-1'])) {
                    continue;
                }

                $outputNames = collect($processVersionCache['process_version_simple']['outputs'])
                    ->map(fn($output) => $output['name'])
                    ->unique()
                    ->toArray();

                foreach ($outputNames as $outputName) {
                    $namespace = $processVersionCache['process_version_simple']['full_namespace_without_version'];
                    $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - Prozess-Daten - ' . $outputName;
                    $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.outputs.' . $outputName . ']]';
                    $items[] = (array) new Item($label, $value, 'reference.outputs');
                }
            }
        }

        return $items;
    }
}
