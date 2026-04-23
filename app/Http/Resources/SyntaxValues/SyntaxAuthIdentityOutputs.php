<?php

namespace App\Http\Resources\SyntaxValues;

use App\Http\Resources\Cache\ProcessVersionCache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxAuthIdentityOutputs
 * Prozessdaten der Prozess-Identität
 * @package App\Http\Resources
 */
class SyntaxAuthIdentityOutputs extends JsonResource {

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

        // URls zu den Prozess-Daten (Artefakte)
        foreach ($outputNames as $outputName) {
            $label = 'Prozess-Identität - Daten - ' . $outputName;
            $value = '[[auth.identity.outputs.' . $outputName . ']]';
            $items[] = (array) new Item($label, $value, 'auth.identity.outputs');
        }

        return $items;
    }
}
