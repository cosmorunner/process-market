<?php

use App\ProcessType\ListConfig;
use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Collection;

/**
 * Recalculates the graphs.
 */
return new class extends Migration  {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    $data = $listConfig->data;
                    $data['enable_label'] = $data['show_label'] ?? true;

                    unset($data['show_label']);

                    $listConfig->data = $data;
                }

                $version->update(['definition' => $definition->toArray()]);

                $version->exportDefinition();
                $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
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
                $definition = $version->definition;

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    $data = $listConfig->data;
                    $data['show_label'] = $data['enable_label'] ?? true;

                    unset($data['enable_label']);

                    $listConfig->data = $data;
                }

                $version->update(['definition' => $definition->toArray()]);

                $version->exportDefinition();
                $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
            }
        });
    }
};
