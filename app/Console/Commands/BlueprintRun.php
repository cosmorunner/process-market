<?php /** @noinspection PhpUnused */

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Class BlueprintRun
 * Unter Angabe einer unter config/blueprints existierenden Blueprint-Konfiguration führt dieser Command
 * einen Datenbank-Seed aus, indem der DatabaseSeeder mit einer bestimmten Blueprint-Konfiguration durchgeführt wird.
 * @package App\Console\Commands
 */
class BlueprintRun extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:blueprint_run {blueprintName}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Leert die Datenbank und führt den DatabaseSeeder mit einer bestimmten Blueprint-Konfiguration aus.';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle() : int {
        $blueprintName = $this->argument('blueprintName');
        $path = config_path('blueprints/' . $blueprintName . '.php');

        if (!file_exists($path)) {
            $this->error(__('exceptions.blueprint_does_not_exist', ['name' => $blueprintName]));

            return 1;
        }

        $this->info(__('info.blueprint_seed_is_being_executed', ['name' => $blueprintName]));

        // Alle Migrations (database/migrations) auf die Datenbank anwenden.
        Artisan::call('migrate:fresh --force');

        // Datenbank wird vollständig neu aufgesetzt und der Blueprint-Seed durchlaufen.
        (new DatabaseSeeder())->run(config('blueprints.' . $blueprintName));
        return 0;
    }
}
