<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxReferenceRelationData
 * Daten der Verknüpfungen
 * @package App\Http\Resources
 */
class SyntaxReferenceRelationData extends JsonResource {

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

        foreach ($this->resource['process_version_simple']['relation_types'] as $relationType) {
            if (!in_array($relationType['connection_type'], ['n-1', '1-1'])) {
                continue;
            }

            foreach (array_keys($relationType['default']) as $name) {
                $label = '[' . $relationType['reference'] . '] - Verknüpfungsdaten - ' . $name;
                $value = '[[process.references.' . $relationType['reference'] . '.data.' . $name . ']]';
                $items[] = (array) new Item($label, $value, 'reference.relation_data');
            }
        }


        // External relationtypes
        foreach ($processVersionCaches as $processVersionCache) {
            foreach ($processVersionCache['process_version_simple']['relation_types'] as $relationType) {
                // Here we switch the "n-1" to "1-n" because when the syntax ist used, it refers to the external relationtype.
                if (!in_array($relationType['connection_type'], ['1-n', '1-1'])) {
                    continue;
                }

                $namespace = $processVersionCache['process_version_simple']['full_namespace_without_version'];

                foreach (array_keys($relationType['default']) as $name) {
                    $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - Verknüpfungsdaten - ' . $name;
                    $value = '[[process.references.' . $namespace . '::' . $relationType['reference'] . '.data.' . $name . ']]';
                    $items[] = (array) new Item($label, $value, 'reference.relation_data');
                }
            }
        }

        return $items;
    }
}
