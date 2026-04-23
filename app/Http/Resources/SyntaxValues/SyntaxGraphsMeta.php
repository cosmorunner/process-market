<?php

namespace App\Http\Resources\SyntaxValues;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxGraphsMeta
 * Initialaktionen-Urls
 * @package App\Http\Resources
 */
class SyntaxGraphsMeta extends JsonResource {

    /**
     * @var array GraphCache
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $namespace = $this->resource['process_version_simple']['namespace'] ?? '';
        $identifier = $this->resource['process_version_simple']['identifier'] ?? '';

        // Aktuellen Prozess hinzufügen
        $label = $namespace . '/' . $identifier . ' - Anzahl Prozesse';
        $value = '[[process_type.' . $namespace . '.' . $identifier . '.meta.count]]';
        $items = [(array)new Item($label, $value, 'graphs.meta')];

        // Daten von externen Graphen hinzufügen
        ($this->additional['processVersionCaches'] ?? collect())->each(function (array $processVersionCache) use (&$items) {
            $namespace = $processVersionCache['process_version_simple']['namespace'] ?? '';
            $identifier = $processVersionCache['process_version_simple']['identifier'] ?? '';

            if ($namespace && $identifier) {
                $label = $namespace . '/' . $identifier . ' - Anzahl Prozesse';
                $value = '[[process_type.' . $namespace . '.' . $identifier . '.meta.count]]';
                $items[] = (array)new Item($label, $value, 'graphs.meta');
            }
        });

        return $items;
    }
}
