<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxReferenceUrls
 * Urls der Verknüpfungen
 * @package App\Http\Resources
 */
class SyntaxReferenceUrls extends JsonResource {

    /**
     * @var ProcessVersion
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

        $listSlugs = collect($processVersionCaches)
            ->map(fn($processVersionCache) => $processVersionCache['process_version_simple']['list_configs'])
            ->flatten(1)
            ->map(fn($listConfig) => $listConfig['slug'])
            ->unique()
            ->toArray();

        foreach ($this->resource['process_version_simple']['relation_types'] as $relationType) {
            $reference = $relationType['reference'];

            if (!in_array($relationType['connection_type'], ['n-1', '1-1'])) {
                continue;
            }

            $label = '[' . $reference . '] - ' . 'Prozess-URL - Übersicht';
            $value = '[[process.references.' . $reference . '.meta.url]]';
            $items[] = (array) new Item($label, $value, 'reference.urls');

            $label = '[' . $reference . '] - ' . 'Prozess-URL - Daten';
            $value = '[[process.references.' . $reference . '.meta.data_url]]';
            $items[] = (array) new Item($label, $value, 'reference.urls');

            // URls zu den Prozess-Daten (Artefakte)
            foreach ($outputNames as $outputName) {
                $label = '[' . $reference . '] - Prozess-URL Artefakt - ' . $outputName;
                $value = '[[process.references.' . $reference . '.urls.outputs.' . $outputName . ']]';
                $items[] = (array) new Item($label, $value, 'reference.urls');
            }

            // URls zu den Listen
            foreach ($listSlugs as $listSlug) {
                $label = '[' . $reference . '] - Prozess-Listen-URL - ' . $listSlug;
                $value = '[[process.references.' . $reference . '.urls.lists.' . $listSlug . ']]';
                $items[] = (array) new Item($label, $value, 'reference.urls');
            }
        }

        // External relationtypes
        foreach ($processVersionCaches as $processVersionCache) {
            foreach ($processVersionCache['process_version_simple']['relation_types'] as $relationType) {
                // Here we switch the "n-1" to "1-n" because when the syntax ist used, it refers to the external relationtype.
                if (!in_array($relationType['connection_type'], ['1-n', '1-1'])) {
                    continue;
                }

                $namespace = $processVersionCache['process_version_simple']['namespace'] . '/' . $processVersionCache['process_version_simple']['identifier'];
                $reference = $relationType['reference'];

                $outputNames = collect($processVersionCache['process_version_simple']['outputs'])
                    ->map(fn($output) => $output['name'])
                    ->unique()
                    ->toArray();

                $listSlugs = collect($processVersionCache['process_version_simple']['list_configs'])
                    ->map(fn($listConfig) => $listConfig['slug'])
                    ->unique()
                    ->toArray();

                $label = '[' . $namespace . ' - ' . $reference . '] - ' . 'Prozess-URL - Übersicht';
                $value = '[[process.references.' . $namespace . '::' . $reference . '.meta.url]]';
                $items[] = (array) new Item($label, $value, 'reference.urls');

                $label = '[' . $namespace . ' - ' . $reference . '] - ' . 'Prozess-URL - Daten';
                $value = '[[process.references.' . $namespace . '::' . $reference . '.meta.data_url]]';
                $items[] = (array) new Item($label, $value, 'reference.urls');

                // URls zu den Prozess-Daten (Artefakte)
                foreach ($outputNames as $outputName) {
                    $label = '[' . $namespace . ' - ' . $reference . '] - Prozess-URL Artefakt - ' . $outputName;
                    $value = '[[process.references.' . $namespace . '::' . $reference . '.urls.outputs.' . $outputName . ']]';
                    $items[] = (array) new Item($label, $value, 'reference.urls');
                }

                // URls zu den Listen
                foreach ($listSlugs as $listSlug) {
                    $label = '[' . $namespace . ' - ' . $reference . '] - Prozess-Listen-URL - ' . $listSlug;
                    $value = '[[process.references.' . $namespace . '::' . $reference . '.urls.lists.' . $listSlug . ']]';
                    $items[] = (array) new Item($label, $value, 'reference.urls');
                }
            }
        }

        return $items;
    }
}
