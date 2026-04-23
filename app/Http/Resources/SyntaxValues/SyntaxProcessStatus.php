<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class SyntaxProcessStatus
 * Process-Status Daten
 * @package App\Http\Resources
 */
class SyntaxProcessStatus extends JsonResource {

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

        $this->resource->definition->statusTypes->each(function (StatusType $statusType) use (&$items) {
            $items[] = (array) new Item('Status - ' . $statusType->name . ' - Wert', '[[process.status.' . $statusType->reference . '.value]]', 'process.status');
            $items[] = (array) new Item('Status - ' . $statusType->name . ' - Beschreibung', '[[process.status.' . $statusType->reference . '.text]]', 'process.status');
            $items[] = (array) new Item('Status - ' . $statusType->name . ' - Aktuelle Zustands-Id', '[[process.status.' . $statusType->reference . '.state]]', 'process.status');

            $statusType->states->each(function (State $state) use ($statusType, &$items) {
                $descr = Str::limit($state->description, 30);

                $label = 'Status - ' . $statusType->name . ' - Zustand - ' . $descr . ' - Id';
                $value = '[[process.process_type.status_type.' . $statusType->reference . '.states.' . $state->id . '.id]]';
                $items[] = (array) new Item($label, $value, 'process.status');

                $label = 'Status - ' . $statusType->name . ' - Zustand - ' . $descr . ' - Min-Wert';
                $value = '[[process.process_type.status_type.' . $statusType->reference . '.states.' . $state->id . '.min]]';
                $items[] = (array) new Item($label, $value, 'process.status');

                $label = 'Status - ' . $statusType->name . ' - Zustand - ' . $descr . ' - Max-Wert';
                $value = '[[process.process_type.status_type.' . $statusType->reference . '.states.' . $state->id . '.max]]';
                $items[] = (array) new Item($label, $value, 'process.status');
            });
        });

        usort($items, function ($a, $b) {
            return $a['label'] > $b['label'];
        });

        return $items;
    }
}
