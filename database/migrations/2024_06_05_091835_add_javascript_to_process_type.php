<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        ProcessVersion::where('version', '=', 'develop')->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $definition['javascript'] = [];

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
                $definition['javascript'] = [];
                $history->update(['definition' => $definition]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        ProcessVersion::where('version', '=', 'develop')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                unset($definition['javascript']);

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
                unset($definition ['javascript']);
                $history->update(['definition' => $definition]);
            }
        });
    }
};
