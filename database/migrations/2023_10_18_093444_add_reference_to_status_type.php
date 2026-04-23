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
                $definition = $version->definition;
                foreach ($definition->statusTypes as $statusType) {
                    $statusType->reference = $statusType->id;
                }

                $version->update(['definition' => $definition->toArray()]);

                $version->exportDefinition();

                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $statusTypes = $definition['status_types'];

                foreach ($statusTypes as $key => $statusType) {
                    $statusTypes[$key]['reference'] = $statusType['id'];
                }

                $definition['status_types'] = $statusTypes;
                $history->update(['definition' => $definition]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $statusTypes = $definition['status_types'];
                foreach ($statusTypes as $key => $statusType) {
                    unset($statusTypes[$key]['reference']);
                }

                $definition['status_types'] = $statusTypes;
                $version->update(['definition' => $definition]);

                $version->exportDefinition();
                $version->exportDefinition('latest');
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $statusTypes = $definition['status_types'];

                foreach ($statusTypes as $key => $statusType) {
                    unset($statusTypes[$key]['reference']);
                }

                $definition['status_types'] = $statusTypes;
                $history->update(['definition' => $definition]);
            }
        });
    }
};
