<?php

namespace App\Http\Resources\SyntaxValues;

use App\ProcessType\ActionType;
use App\ProcessType\Output;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxActionArtifacts
 * Aktionstyp-Artefakt Daten
 * @package App\Http\Resources
 */
class SyntaxActionArtifacts extends JsonResource {

    /**
     * @var ActionType
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $items = [];

        $this->resource->outputs->each(function(Output $output) use (&$items) {
            $items[] = (array)new Item('Aktion-Artefakt - ' . $output->name . ' - Name', '[[action.artifacts.' . $output->name . '.name]]', 'action.artifacts');
            $items[] = (array)new Item('Aktion-Artefakt - ' . $output->name . ' - Dateiname', '[[action.artifacts.' . $output->name . '.file_name]]', 'action.artifacts');
            $items[] = (array)new Item('Aktion-Artefakt - ' . $output->name . ' - Inhalt als Base64', '[[action.artifacts.' . $output->name . '.base64]]', 'action.artifacts');
            $items[] = (array)new Item('Aktion-Artefakt - ' . $output->name . ' - Mime-Type', '[[action.artifacts.' . $output->name . '.mime_type]]', 'action.artifacts');
            $items[] = (array)new Item('Aktion-Artefakt - ' . $output->name . ' - SHA 256', '[[action.artifacts.' . $output->name . '.sha_256]]', 'action.artifacts');
            $items[] = (array)new Item('Aktion-Artefakt - ' . $output->name . ' - Erstelldatum Timestamp', '[[action.artifacts.' . $output->name . '.created_at]]', 'action.artifacts');
        })->toArray();

        usort($items, function($a, $b) {
            return $a['label'] > $b['label'];
        });

        return $items;
    }
}
