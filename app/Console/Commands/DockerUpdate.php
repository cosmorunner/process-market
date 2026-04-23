<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

/**
 * Setup the app when the docker image is updated.
 * Class DockerUpdate
 * @package App\Console\Commands
 */
class DockerUpdate extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:docker_update';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Automatic migration/session/cache cleaning on docker image update';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int {
        $this->info('Docker update: Running...');
        $output = 0;

        // Check if database exists.
        try {
            if (Schema::hasTable('users')) {
                $this->info('Docker update: Migrating database...');
                Artisan::call('migrate --force');
                $this->info('Docker update: Migration complete.');
            }

            // If any of these fail the error code will be bit shifted to a > 0 number.
            // Each number should be unique to the scenario.
            $output |= (Artisan::call('redis:flushdb') << 0);
            $output |= (Artisan::call('cache:clear') << 1);
            $output |= (Artisan::call('config:clear') << 2);
            $output |= (Artisan::call('view:clear') << 3);
            $output |= (Artisan::call('event:clear') << 4);
            $output |= (Artisan::call('route:clear') << 5);
            $output |= (Artisan::call('app:session_flush') << 6);

        }
        catch (Exception $exception) {
            $this->info($exception->getMessage());
            // In a fresh installation we don't have a database yet, so we will get an exception,
            // So in this case there is no need to clear any cache files or run migrations.
        }

        // Optimize the performance. Do not cache config.
        if (App::environment() === 'production') {
            Artisan::call('route:cache');
            Artisan::call('event:cache');
            Artisan::call('view:cache');
        }

        $this->info('Docker update: Complete.');

        return $output;
    }
}
