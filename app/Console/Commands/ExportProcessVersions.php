<?php

namespace App\Console\Commands;

use App\Models\ProcessVersion;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Collection;

/**
 * Exports the process versions.
 * Class DockerUpdate
 * @package App\Console\Commands
 */
class ExportProcessVersions extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:export_process_versions';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Exports the process versions.';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int {
        $this->info('Export process versions...');

        $output = 0;

        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) use (&$output) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $version->exportDefinition();
                try {
                    $version->exportDependencies();
                }
                catch (FileNotFoundException $e) {
                    $this->error($e->getMessage());

                    $output = 1;
                }

                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                    try {
                        $version->exportDependencies('latest');
                    }
                    catch (FileNotFoundException $e) {
                        $this->error($e->getMessage());

                        $output = 2;
                    }

                }
            }
        });

        return $output;
    }
}
