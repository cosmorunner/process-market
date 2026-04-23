<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\Output;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxProcessArtifacts
 * Daten zu Prozess-Artefakten
 * @package App\Http\Resources
 */
class SyntaxProcessArtifacts extends JsonResource {

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

        $this->resource->definition->outputs->each(function(Output $output) use(&$items) {
            $items[] = (array)new Item('Prozess-Artefakt - ' . $output->name . ' - Name', '[[process.artifacts.' . $output->name . '.name]]', 'process.artifacts');
            $items[] = (array)new Item('Prozess-Artefakt - ' . $output->name . ' - Dateiname', '[[process.artifacts.' . $output->name . '.file_name]]', 'process.artifacts');
            $items[] = (array)new Item('Prozess-Artefakt - ' . $output->name . ' - Inhalt als Base64', '[[process.artifacts.' . $output->name . '.base64]]', 'process.artifacts');
            $items[] = (array)new Item('Prozess-Artefakt - ' . $output->name . ' - Mime-Type', '[[process.artifacts.' . $output->name . '.mime_type]]', 'process.artifacts');
            $items[] = (array)new Item('Prozess-Artefakt - ' . $output->name . ' - SHA 256', '[[process.artifacts.' . $output->name . '.sha_256]]', 'process.artifacts');
            $items[] = (array)new Item('Prozess-Artefakt - ' . $output->name . ' - Erstelldatum Timestamp', '[[process.artifacts.' . $output->name . '.created_at]]', 'process.artifacts');
        });

        usort($items, function($a, $b) {
            return $a['label'] > $b['label'];
        });

        return $items;
    }
}
