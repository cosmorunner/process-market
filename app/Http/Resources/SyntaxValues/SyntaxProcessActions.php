<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Processor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxProcessActions
 * Values of tagged process actions.
 * @package App\Http\Resources
 */
class SyntaxProcessActions extends JsonResource {

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

        $this->resource->definition->actionTypes->each(function (ActionType $actionType) use (&$items) {
            /* @var Processor $processor */
            foreach ($actionType->processors as $processor) {
                if ($processor->identifier === 'tag_action') {
                    $tag = $processor->options['tag'];
                    $items[] = (array) new Item("[$tag] Prozess-Aktion - Erstelldatum", "[[process.actions.$tag.meta.created_at(format=d.m.Y)]]", 'process.actions');
                    $items[] = (array) new Item("[$tag] Prozess-Aktion - Aktionstyp-Name", "[[process.actions.$tag.meta.action_type_name]]", 'process.actions');
                    $items[] = (array) new Item("[$tag] Prozess-Aktion - Datenfeld - DATENFELD_NAME", "[[process.actions.$tag.outputs.DATENFELD_NAME]]", 'process.actions');
                }
            }
        });

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return array_values(array_unique($items, SORT_REGULAR));
    }
}
