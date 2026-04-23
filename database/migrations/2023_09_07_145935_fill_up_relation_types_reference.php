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
                $definition = $version->definition;
                $updated = false;

                foreach ($definition->relationTypes as $relationType) {
                    if (!$relationType->reference) {
                        $relationType->reference = substr($relationType->id, 0, 5) . '_' . Str::slug($relationType->name, '_');
                        $updated = true;
                    }
                }

                if ($updated) {
                    $version->update(['definition' => $definition->toArray()]);

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
                $updated = false;

                foreach ($relationTypes as $key => $relationType) {
                    if (!$relationType['reference']) {
                        $relationTypes[$key]['reference'] = substr($relationType['id'], 0, 5) . '_' . Str::slug($relationType['name'], '_');
                        $updated = true;
                    }
                }

                if ($updated) {
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
        // No need to down method.
    }
};
