<?php /** @noinspection PhpUnused */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

/**
 * Initializes the platform when being used with docker. Runs the "demo" blueprint, creates an administrator and informs the administrator.
 * Class DockerInstall
 * @package App\Console\Commands
 */
class DockerInstall extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:docker_install {blueprintName}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Installiert die Datenbank (Demo Blueprint).';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int {
        $blueprintName = $this->argument('blueprintName');

        // Check if database exists
        if (Schema::hasTable('users')) {
            $this->info('Database already installed.');

            return 0;
        }

        // If not, execute the given blueprint
        $output = Artisan::call('app:blueprint_run', ['blueprintName' => $blueprintName]);

        if ($output === 0) {
            $this->info(__('info.successfully_installed_via_docker', ['blueprintName' => $blueprintName]));
        }

        return $output;
    }
}
