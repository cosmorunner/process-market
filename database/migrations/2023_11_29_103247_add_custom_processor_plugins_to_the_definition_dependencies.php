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

                $dependencies = $definition['dependencies'];
                $dependencies['custom_processor_plugins'] = [];
                $definition['dependencies'] = $dependencies;

                $version->update(['definition' => $definition]);
                $version->exportDefinition();
                $version->exportDependencies();

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

                $dependencies = $definition['dependencies'];
                $dependencies['custom_processor_plugins'] = [];
                $definition['dependencies'] = $dependencies;

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

                $dependencies = $definition['dependencies'];
                unset($dependencies['custom_processor_plugins']);
                $definition['dependencies'] = $dependencies;

                $version->update(['definition' => $definition]);
                $version->exportDefinition();
                $version->exportDependencies();

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

                $dependencies = $definition['dependencies'];
                unset($dependencies['custom_processor_plugins']);
                $definition['dependencies'] = $dependencies;

                $history->update(['definition' => $definition]);
            }
        });
    }
};
