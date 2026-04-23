<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\ListConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::where('version', '=', 'develop')->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $listConfigs = $definition->listConfigs;
                $isUpdated = false;

                /* @var ListConfig $listConfig */
                foreach ($listConfigs as $listConfig) {
                    if ($listConfig->template != 'process_artifacts') {
                        continue;
                    }

                    $data = $listConfig->data;
                    $data['source']['select'][] = 'actions.action_type_id as actions_action_type_id';
                    $data['source']['select'][] = 'actions.process_id as actions_process_id';

                    $data['additional_filters'] = [
                        [
                            'type' => 'permissions',
                            'type_options' => [
                                'abilities' => 'view_action_artifacts',
                                'arguments' => [
                                    [
                                        'model' => 'process',
                                        'relations' => ['processType'],
                                        'source' => 'actions_process_id',
                                    ],
                                    [
                                        'model' => '',
                                        'relations' => [],
                                        'source' => 'actions_action_type_id',
                                    ],
                                    [
                                        'model' => 'artifact',
                                        'relations' => [],
                                        'source' => 'artifacts_id',
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $listConfig->data = $data;
                    $isUpdated = true;
                }

                if ($isUpdated) {
                    $version->update(['definition' => $definition->toArray()]);
                    $version->exportDefinition();

                    // Export latest version
                    if ($version->process->latest_published_version_id === $version->id) {
                        $version->exportDefinition('latest');
                    }
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $listConfigs = $definition->listConfigs;
                $isUpdated = false;

                /* @var ListConfig $listConfig */
                foreach ($listConfigs as $listConfig) {
                    if ($listConfig->template != 'process_artifacts') {
                        continue;
                    }

                    $data = $listConfig->data;
                    $data['source']['select'][] = 'actions.action_type_id as actions_action_type_id';
                    $data['source']['select'][] = 'actions.process_id as actions_process_id';

                    $data['additional_filters'] = [
                        [
                            'type' => 'permissions',
                            'type_options' => [
                                'abilities' => 'view_action_artifacts',
                                'arguments' => [
                                    [
                                        'model' => 'process',
                                        'relations' => ['processType'],
                                        'source' => 'actions_process_id',
                                    ],
                                    [
                                        'model' => '',
                                        'relations' => [],
                                        'source' => 'actions_action_type_id',
                                    ],
                                    [
                                        'model' => 'artifact',
                                        'relations' => [],
                                        'source' => 'artifacts_id',
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $listConfig->data = $data;
                    $isUpdated = true;
                }

                if ($isUpdated) {
                    $history->update(['definition' => $definition->toArray()]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::where('version', '=', 'develop')->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $listConfigs = $definition->listConfigs;
                $isUpdated = false;

                /* @var ListConfig $listConfig */
                foreach ($listConfigs as $listConfig) {
                    if ($listConfig->template != 'process_artifacts') {
                        continue;
                    }

                    $data = $listConfig->data;
                    unset($data['additional_filters']);

                    $data['source']['select'] = array_values(array_filter($data['source']['select'], function ($item) {
                        return !in_array($item, [
                            'actions.action_type_id as actions_action_type_id',
                            'actions.process_id as actions_process_id'
                        ]);
                    }));

                    $listConfig->data = $data;
                    $isUpdated = true;
                }

                if ($isUpdated) {
                    $version->update(['definition' => $definition->toArray()]);
                    $version->exportDefinition();

                    // Export latest version
                    if ($version->process->latest_published_version_id === $version->id) {
                        $version->exportDefinition('latest');
                    }
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $listConfigs = $definition->listConfigs;
                $isUpdated = false;

                /* @var ListConfig $listConfig */
                foreach ($listConfigs as $listConfig) {
                    if ($listConfig->template != 'process_artifacts') {
                        continue;
                    }

                    $data = $listConfig->data;
                    unset($data['additional_filters']);

                    $data['source']['select'] = array_values(array_filter($data['source']['select'], function ($item) {
                        return !in_array($item, [
                            'actions.action_type_id as actions_action_type_id',
                            'actions.process_id as actions_process_id'
                        ]);
                    }));

                    $listConfig->data = $data;
                    $isUpdated = true;
                }

                if ($isUpdated) {
                    $history->update(['definition' => $definition->toArray()]);
                }
            }
        });
    }
};
