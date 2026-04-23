<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxReferenceStatus
 * Statusdaten der referenzierten Prozesse.
 * @package App\Http\Resources
 */
class SyntaxReferenceStatus extends JsonResource {

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

            foreach ($processVersionCaches as $processVersionCache) {
                foreach ($processVersionCache['process_version_simple']['status_types'] as $statusType) {
                    $namespace = $processVersionCache['process_version_simple']['namespace'] . '/' . $processVersionCache['process_version_simple']['identifier'];
                    $reference = $relationType['reference'];

                    $label = '[' . $reference . '] - ' . $namespace . ' - Status - ' . $statusType['name'] . ' - Wert';
                    $value = '[[process.references.' . $reference . '.status.' . $statusType['reference'] . '.value]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');

                    $label = '[' . $reference . '] - ' . $namespace . ' - Status - ' . $statusType['name'] . ' - Beschreibung';
                    $value = '[[process.references.' . $reference . '.status.' . $statusType['reference'] . '.text]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');

                    $label = '[' . $reference . '] - ' . $namespace . ' - Status - ' . $statusType['name'] . ' - Farbe';
                    $value = '[[process.references.' . $reference . '.status.' . $statusType['reference'] . '.color]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');

                    $label = '[' . $reference . '] - ' . $namespace . ' - Status - ' . $statusType['name'] . ' - Icon';
                    $value = '[[process.references.' . $reference . '.status.' . $statusType['reference'] . '.image]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');
                }
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

                foreach ($processVersionCache['process_version_simple']['status_types'] as $statusType) {
                    $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - Status - ' . $statusType['name'] . ' - Wert';
                    $value = '[[process.references.' . $namespace . '::' . $reference . '.status.' . $statusType['reference'] . '.value]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');

                    $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - Status - ' . $statusType['name'] . ' - Beschreibung';
                    $value = '[[process.references.' . $namespace . '::' . $reference . '.status.' . $statusType['reference'] . '.text]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');

                    $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - Status - ' . $statusType['name'] . ' - Farbe';
                    $value = '[[process.references.' . $namespace . '::' . $reference . '.status.' . $statusType['reference'] . '.color]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');

                    $label = '[' . $namespace . ' - ' . $relationType['reference'] . '] - Status - ' . $statusType['name'] . ' - Icon';
                    $value = '[[process.references.' . $namespace . '::' . $reference . '.status.' . $statusType['reference'] . '.image]]';
                    $items[] = (array) new Item($label, $value, 'reference.status');
                }
            }
        }

        return $items;
    }
}
