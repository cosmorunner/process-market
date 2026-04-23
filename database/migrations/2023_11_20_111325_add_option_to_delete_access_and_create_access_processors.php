<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $actionTypes = $definition['action_types'];
                foreach ($actionTypes as $actionTypeKey => $actionType) {
                    foreach ($actionType['processors'] as $processorKey => $processor) {
                        if ($processor['identifier'] === 'create_access' || $processor['identifier'] === 'delete_access') {
                            $actionTypes[$actionTypeKey]['processors'][$processorKey]['options']['is_public_role'] = false;
                        }
                    }
                }

                $definition['action_types'] = $actionTypes;
                $version->update(['definition' => $definition]);
                $version->exportDefinition();
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                    $version->exportDependencies('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $actionTypes = $definition['action_types'];
                foreach ($actionTypes as $actionTypeKey => $actionType) {
                    foreach ($actionType['processors'] as $processorKey => $processor) {
                        if ($processor['identifier'] === 'create_access' || $processor['identifier'] === 'delete_access') {
                            $actionTypes[$actionTypeKey]['processors'][$processorKey]['options']['is_public_role'] = false;
                        }
                    }
                }

                $definition['action_types'] = $actionTypes;
                $history->update(['definition' => $definition]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $actionTypes = $definition['action_types'];
                foreach ($actionTypes as $actionTypeKey => $actionType) {
                    foreach ($actionType['processors'] as $processorKey => $processor) {
                        if ($processor['identifier'] === 'create_access' || $processor['identifier'] === 'delete_access') {
                            unset($actionTypes[$actionTypeKey]['processors'][$processorKey]['options']['is_public_role']);
                        }
                    }
                }

                $definition['action_types'] = $actionTypes;
                $version->update(['definition' => $definition]);
                $version->exportDefinition();
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                    $version->exportDependencies('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $actionTypes = $definition['action_types'];
                foreach ($actionTypes as $actionTypeKey => $actionType) {
                    foreach ($actionType['processors'] as $processorKey => $processor) {
                        if ($processor['identifier'] === 'create_access' || $processor['identifier'] === 'delete_access') {
                            unset($actionTypes[$actionTypeKey]['processors'][$processorKey]['options']['is_public_role']);
                        }
                    }
                }

                $definition['action_types'] = $actionTypes;
                $history->update(['definition' => $definition]);
            }
        });
    }
};
