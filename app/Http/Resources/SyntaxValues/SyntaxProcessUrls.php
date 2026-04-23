<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\ListConfig;
use App\ProcessType\Output;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxProcessUrls
 * Syntax für Prozess-Urls
 * @package App\Http\Resources
 */
class SyntaxProcessUrls extends JsonResource {

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
        $items = [
            (array) new Item('Prozess-URL - Übersicht', '[[process.meta.url]]', 'process.urls'),
            (array) new Item('Prozess-URL - Daten', '[[process.meta.data_url]]', 'process.urls'),
        ];
        $definition = $this->resource->definition;

        $outputUrls = $definition->outputs->map(function (Output $output) {
            return new Item('Prozess-Artefakt-URL - ' . $output->name, '[[process.urls.outputs.' . $output->name . ']]', 'process.urls');
        })->toArray();

        $listUrls = $definition->listConfigs->map(function (ListConfig $listConfig) {
            return new Item('Prozess-Listen-URL - ' . $listConfig->slug, '[[process.urls.lists.' . $listConfig->slug . ']]', 'process.urls');
        })->toArray();

        $actionUrls = $definition->actionTypes->map(function (ActionType $actionType) {
            return new Item('Prozess-Aktion-URL - ' . $actionType->reference, '[[process.urls.action_types.' . $actionType->reference . ']]', 'process.urls');
        })->toArray();

        return [...$items, ...$outputUrls, ...$listUrls, ...$actionUrls];
    }
}
