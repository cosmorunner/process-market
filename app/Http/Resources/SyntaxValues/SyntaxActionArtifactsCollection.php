<?php

namespace App\Http\Resources\SyntaxValues;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxActionArtifactsCollection
 * Aktionstyp-Artefakt Daten
 * @package App\Http\Resources
 */
class SyntaxActionArtifactsCollection extends JsonResource {

    /**
     * @var array ProcessVersionCache
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = [];

        foreach ($this->resource['process_version_simple']['action_types'] as $actionType) {
            foreach ($actionType['outputs'] as $output) {
                $items[] = (array)new Item('Aktion-Artefakt - ' . $output['name'] . ' - Name', '[[action.artifacts.' . $output['name'] . '.name]]', 'action.artifacts');
                $items[] = (array)new Item('Aktion-Artefakt - ' . $output['name'] . ' - Dateiname', '[[action.artifacts.' . $output['name'] . '.file_name]]', 'action.artifacts');
                $items[] = (array)new Item('Aktion-Artefakt - ' . $output['name'] . ' - Inhalt als Base64', '[[action.artifacts.' . $output['name'] . '.base64]]', 'action.artifacts');
                $items[] = (array)new Item('Aktion-Artefakt - ' . $output['name'] . ' - Mime-Type', '[[action.artifacts.' . $output['name'] . '.mime_type]]', 'action.artifacts');
                $items[] = (array)new Item('Aktion-Artefakt - ' . $output['name'] . ' - SHA 256', '[[action.artifacts.' . $output['name'] . '.sha_256]]', 'action.artifacts');
                $items[] = (array)new Item('Aktion-Artefakt - ' . $output['name'] . ' - Erstelldatum Timestamp', '[[action.artifacts.' . $output['name'] . '.created_at]]', 'action.artifacts');
            }
        }

        usort($items, function($a, $b) {
            return $a['label'] > $b['label'];
        });

        return array_values(array_unique($items, SORT_REGULAR));

    }
}
