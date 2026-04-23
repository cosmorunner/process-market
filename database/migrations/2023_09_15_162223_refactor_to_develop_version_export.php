<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Collection;

/**
 * Recalculates the graphs.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {

                $version->exportDefinition();
                $version->exportDependencies();

                // Export develop version and update process "latest_version" and update definition
                if ($version->process->latest_version_id === $version->id) {
                    $version->update([
                        'version' => 'develop',
                        'definition->version' => 'develop',
                        'full_namespace' => $version->process->full_namespace . '@develop'
                    ]);

                    $version->exportDefinition('develop');
                    $version->exportDependencies('develop');
                    $version->process->update(['latest_version' => 'develop']);
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
                $latestPublishedVersion = $version->process->latestPublishedVersion;
                $developVersion = '0.0.1';

                if($latestPublishedVersion) {
                    $parts = explode('.', $latestPublishedVersion->version);
                    $lastPart = (int) ($parts[2]);
                    $lastPart++;
                    $developVersion = implode('.', [$parts[0], $parts[1], $lastPart]);
                }


                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                    $version->exportDependencies('latest');
                }

                // Export develop version and update process "latest_version" and update definition
                if ($version->process->latest_version_id === $version->id) {
                    $version->update([
                        'version' => $developVersion,
                        'definition->version' => $developVersion,
                        'full_namespace' => $version->process->full_namespace . '@' . $developVersion
                    ]);

                    $version->exportDefinition($developVersion);
                    $version->exportDependencies($developVersion);
                    $version->exportDefinition('latest');
                    $version->exportDependencies('latest');
                    $version->process->update(['latest_version' => $developVersion]);
                }
            }
        });
    }
};
