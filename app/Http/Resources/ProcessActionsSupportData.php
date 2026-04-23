<?php

namespace App\Http\Resources;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ExecutedActionsSupportData
 * Gibt die Support-Data für eine Listenkonfiguration vom Template "executed_actions" zurück.
 * @package App\Http\Resources
 */
class ProcessActionsSupportData extends BaseSupportData {

    /**
     * @var Process
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $actionAttributes = [...collect(config('list_config.columns.actions'))->values()];

        $additionalMetadataColumns = [
            [
                'column' => 'processes.name',
                'alias' => 'processes_name',
                'label' => 'Prozess - Name'
            ],
            [
                'column' => 'processes.id',
                'alias' => 'processes_id',
                'label' => 'Prozess - Id'
            ],
            [
                'column' => "CONCAT('process|', processes.id, '[', processes.name, ']')",
                'alias' => 'processes_pipe_notation',
                'label' => 'Prozess - Pipe-Notation'
            ],
            [
                'column' => 'users.first_name',
                'alias' => 'users_first_name',
                'label' => 'Aktion - Benutzer - Vorname'
            ],
            [
                'column' => 'users.last_name',
                'alias' => 'users_last_name',
                'label' => 'Aktion - Benutzer - Nachname'
            ],
            [
                'column' => "CONCAT(users.first_name, ' ', users.last_name) as users_full_name",
                'alias' => 'users_full_name',
                'label' => 'Aktion - Benutzer - Vor- und Nachname'
            ],
            [
                'column' => "CONCAT('process|', users.identity_id, '[', users.first_name, ' ', users.last_name, ']') as users_identity_pipe_notation",
                'alias' => 'users_identity_pipe_notation',
                'label' => 'Aktion - Benutzer - Prozess-Identität Pipe-Notation'
            ]
        ];

        $actionDataColumns = [];
        $uniqueOutputs = $this->processVersion->definition->actionTypes->pluck('outputs')->flatten(1)->unique('name');

        foreach ($uniqueOutputs as $uniqueOutput) {
            $actionDataColumns[] = [
                'column' => 'actions.action_data->' . $uniqueOutput->name,
                'alias' => 'actions_action_data_' . $uniqueOutput->name,
                'label' => 'Aktion-Daten - ' . $uniqueOutput->name
            ];
        }

        $metaDataColumns = [...$actionAttributes, ...$additionalMetadataColumns];
        $statusDataColumns = $this->statusTypeSupportData($this->processVersion->definition->statusTypes);

        $baseSelectItems = collect($metaDataColumns)->map(fn($item) => [
            'column' => $item['column'],
            'alias' => $item['alias']
        ])->toArray();

        return [
            'defaultColumns' => $metaDataColumns,
            'actionDataColumns' => $actionDataColumns,
            'statusDataColumns' => $this->statusTypeSupportData($this->processVersion->definition->statusTypes),
            'allColumns' => [...$metaDataColumns, ...$actionDataColumns, ...$statusDataColumns],
            'select' => array_merge($this->getSelectItems(), $baseSelectItems)
        ];
    }

    /**
     * Gibt die Alias/Spalten/Label Daten für eine Statustypen-Collection zurück.
     * @param Collection $statusTypes
     * @return array
     */
    protected function statusTypeSupportData(Collection $statusTypes): array {
        $items = [];

        foreach ($statusTypes as $statusType) {
            $alias = str_replace('-', '_', $statusType->reference);

            $items[] = [
                'column' => 'actions.status_data->' . $statusType->reference . '->value::numeric',
                'alias' => 'actions_status_data_' . $alias . '_value',
                'label' => 'Aktions-Statusveränderung - ' . $statusType->reference . ' - Wert'
            ];
            $items[] = [
                'column' => 'actions.status_data->' . $statusType->reference . '->text_value',
                'alias' => 'actions_status_data_' . $alias . '_text_value',
                'label' => 'Aktions-Statusveränderung - ' . $statusType->reference . ' - Text-Wert'
            ];
            $items[] = [
                'column' => 'actions.status_data->' . $statusType->reference . '->color',
                'alias' => 'actions_status_data_' . $alias . '_color',
                'label' => 'Aktions-Statusveränderung - ' . $statusType->reference . ' - Farbe'
            ];
            $items[] = [
                'column' => 'actions.status_data->' . $statusType->reference . '->image',
                'alias' => 'actions_status_data_' . $alias . '_image',
                'label' => 'Aktions-Statusveränderung - ' . $statusType->reference . ' - Icon'
            ];
        }

        return $items;
    }

}
