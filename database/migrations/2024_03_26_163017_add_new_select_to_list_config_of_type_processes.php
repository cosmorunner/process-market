<?php

use App\Models\ProcessVersionHistory;
use App\ProcessType\ListConfig;
use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->with('process')->where('version', '=', 'develop')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $updated = false;

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    if ($listConfig->template !== 'processes') {
                        continue;
                    }

                    $updated = true;
                    $data = $listConfig->data;

                    array_unshift($data['source']['select'], 'owner_processes.id as owner_processes_id');

                    $data['source']['leftJoin'][] = [
                        'table' => 'processes as owner_processes',
                        'on' => [
                            [
                                'owner_processes.id',
                                '=',
                                '[[process.meta.id]]'
                            ]
                        ]
                    ];

                    $listConfig->data = $data;
                }

                if ($updated) {
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
                $updated = false;

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    if ($listConfig->template !== 'processes') {
                        continue;
                    }

                    $updated = true;
                    $data = $listConfig->data;

                    array_unshift($data['source']['select'], 'owner_processes.id as owner_processes_id');

                    $data['source']['leftJoin'][] = [
                        'table' => 'processes as owner_processes',
                        'on' => [
                            [
                                'owner_processes.id',
                                '=',
                                '[[process.meta.id]]'
                            ]
                        ]
                    ];

                    $listConfig->data = $data;
                }

                if ($updated) {
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
        ProcessVersion::query()->with('process')->where('version', '=', 'develop')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $updated = false;

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    if ($listConfig->template !== 'processes') {
                        continue;
                    }

                    $updated = true;
                    $data = $listConfig->data;

                    $data['source']['select'] = array_values(array_filter($data['source']['select'], function ($value) {
                        return $value !== 'owner_processes.id as owner_processes_id';
                    }));

                    unset($data['source']['leftJoin']);

                    $listConfig->data = $data;
                }

                if ($updated) {
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
                $updated = false;

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    if ($listConfig->template !== 'processes') {
                        continue;
                    }

                    $updated = true;
                    $data = $listConfig->data;

                    $data['source']['select'] = array_values(array_filter($data['source']['select'], function ($value) {
                        return $value !== 'owner_processes.id as owner_processes_id';
                    }));

                    unset($data['source']['leftJoin']);

                    $listConfig->data = $data;
                }

                if ($updated) {
                    $history->update(['definition' => $definition->toArray()]);
                }
            }
        });
    }
};
