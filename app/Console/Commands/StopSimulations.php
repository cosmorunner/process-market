<?php

namespace App\Console\Commands;

use App\Models\Simulation;
use App\SimulationConnector;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Flushed die Redis Datenbank.
 * Class FlushRedis
 * @package App\Console\Commands
 */
class StopSimulations extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:stop_simulations';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Stoppt alle laufenden Simulationen.';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle() : int {
        Simulation::whereFinishedAt(null)->get()->each(function (Simulation $simulation) {
            // Exportierten Prozesstyp und Graphen löschen.
            Storage::delete($simulation->definitionExportFilePath());
            Storage::delete($simulation->graphExportFilePath());

            $simulation->markAsFinished();

            try {
                $connector = new SimulationConnector($simulation);
                $connector->deleteAllisaSimulation();
                $this->info('Stopped simulation ' . $simulation->id);
                return 0;
            }
            catch (GuzzleException|BadResponseException $exception) {
                $this->error('Error stopping simulation ' . $simulation->id . ': ' . $exception->getMessage());
                // Ignorieren, hauptsache Simulation in der Prozessfabrik wurde beendet.
                return 1;
            }
        });
        return 0;
    }
}
