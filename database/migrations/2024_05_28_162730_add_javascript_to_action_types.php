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
        ProcessVersion::where('version', '=', 'develop')->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $actionTypes = $definition['action_types'];

                foreach ($actionTypes as $key => $actionType) {
                    $actionTypes[$key]['javascript'] = [];
                }

                $definition['action_types'] = $actionTypes;
                $version->update(['definition' => $definition]);
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->getRawDefintion();
                $actionTypes = $definition['action_types'];

                foreach ($actionTypes as $key => $actionType) {
                    $actionTypes[$key]['javascript'] = [];
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
        ProcessVersion::where('version', '=', 'develop')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $actionTypes = $definition['action_types'];

                foreach ($actionTypes as $key => $actionType) {
                    unset($actionTypes[$key]['javascript']);
                }

                $definition['action_types'] = $actionTypes;
                $version->update(['definition' => $definition]);
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->getRawDefintion();
                $actionTypes = $definition['action_types'];

                foreach ($actionTypes as $key => $actionType) {
                    unset($actionTypes[$key]['javascript']);
                }

                $definition['action_types'] = $actionTypes;
                $history->update(['definition' => $definition]);
            }
        });
    }
};
