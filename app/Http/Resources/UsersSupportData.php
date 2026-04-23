<?php

namespace App\Http\Resources;

use App\Models\Process;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class UsersSupportData
 * Returns the support data for the "users" list template
 * @package App\Http\Resources
 */
class UsersSupportData extends BaseSupportData {

    /**
     * @var Collection|Process[]
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array {
        $userAttributes = [...collect(config('list_config.columns.users'))->values()];
        $baseSelectItems = collect($userAttributes)->map(fn($item) => [
            'column' => $item['column'],
            'alias' => $item['alias']
        ])->toArray();

        $latestProcessVersions = $this->resource->pluck('latestPublishedVersion')
            ->merge([$this->processVersion])
            ->filter(fn(ProcessVersion $processVersion) => $processVersion->isValidIdentityVersion());

        $outputs = [];

        /* @var ProcessVersion $latestProcessVersion */
        foreach ($latestProcessVersions as $latestProcessVersion) {
            foreach ($latestProcessVersion->definition->outputs as $output) {
                $outputs[] = [
                    'column' => 'processes.data->' . $output->name,
                    'alias' => 'processes_data_' . $output->name,
                    'label' => 'Prozess-Daten - ' . $output->name
                ];
            }

            $outputs = collect($outputs)->unique('alias')->toArray();
        }

        return [
            'outputs' => $outputs,
            'allColumns' => $userAttributes,
            'defaultColumns' => $userAttributes,
            'select' => array_merge($this->getSelectItems(), $baseSelectItems)
        ];
    }

}
