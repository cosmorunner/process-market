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
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $relationTypes = $definition['relation_types'];

                foreach ($relationTypes as $key => $relationType) {
                    $relationTypes[$key]['connection_type'] = $relationType['single'] ? 'n-1' : 'n-n';

                    unset($relationTypes[$key]['single']);
                }

                if (count($relationTypes) > 0) {
                    $definition['relation_types'] = $relationTypes;
                    $version->update(['definition' => $definition]);

                    $version->exportDefinition();
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $relationTypes = $definition['relation_types'];

                foreach ($relationTypes as $key => $relationType) {
                    $relationTypes[$key]['connection_type'] = $relationType['single'] ? 'n-1' : 'n-n';

                    unset($relationTypes[$key]['single']);
                }

                if (count($relationTypes) > 0) {
                    $definition['relation_types'] = $relationTypes;
                    $history->update(['definition' => $definition]);
                }
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
                $relationTypes = $definition['relation_types'];

                foreach ($relationTypes as $key => $relationType) {
                    $relationTypes[$key]['single'] = ($relationType['connection_type'] === 'n-1');

                    unset($relationTypes[$key]['connection_type']);
                }

                if (count($relationTypes) > 0) {
                    $definition['relation_types'] = $relationTypes;
                    $version->update(['definition' => $definition]);

                    $version->exportDefinition();
                    $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $relationTypes = $definition['relation_types'];

                foreach ($relationTypes as $key => $relationType) {
                    $relationTypes[$key]['single'] = ($relationType['connection_type'] === 'n-1');

                    unset($relationTypes[$key]['connection_type']);
                }

                if (count($relationTypes) > 0) {
                    $definition['relation_types'] = $relationTypes;
                    $history->update(['definition' => $definition]);
                }
            }
        });
    }
};
