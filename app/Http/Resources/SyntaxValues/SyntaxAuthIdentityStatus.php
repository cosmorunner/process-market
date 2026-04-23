<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxAuthIdentityStatus
 * Statusdaten der Prozess-Identität.
 * @package App\Http\Resources
 */
class SyntaxAuthIdentityStatus extends JsonResource {

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

        foreach ($processVersionCaches as $processVersionCache) {
            foreach ($processVersionCache['process_version_simple']['status_types'] as $statusType) {
                $namespace = $processVersionCache['process_version_simple']['namespace'] . '/' . $processVersionCache['process_version_simple']['identifier'];

                // Exakte Version
                $label = 'Prozess-Identität - Status - ' . $namespace . ' - ' . $statusType['name'] . ' - Wert';
                $value = '[[auth.identity.status.' . $statusType['reference'] . '.value]]';
                $items[] = (array) new Item($label, $value, 'auth.identity.status');

                $label = 'Prozess-Identität - Status - ' . $namespace . ' - ' . $statusType['name'] . ' - Beschreibung';
                $value = '[[auth.identity.status.' . $statusType['reference'] . '.text]]';
                $items[] = (array) new Item($label, $value, 'auth.identity.status');

                $label = 'Prozess-Identität - Status - ' . $namespace . ' - ' . $statusType['name'] . ' - Farbe';
                $value = '[[auth.identity.status.' . $statusType['reference'] . '.color]]';
                $items[] = (array) new Item($label, $value, 'auth.identity.status');

                $label = 'Prozess-Identität - Status - ' . $namespace . ' - ' . $statusType['name'] . ' - Icon';
                $value = '[[auth.identity.status.' . $statusType['reference'] . '.image]]';
                $items[] = (array) new Item($label, $value, 'auth.identity.status');
            }
        }

        return $items;
    }
}
