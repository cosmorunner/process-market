<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use App\Models\ProcessVersion;

/**
 * Ändert in der "processes" Tabelle die Spalte "latest_graph_id" zu "latest_version_id" und
 * "latest_published_graph_id" zu "latest_published_version_id".
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $processVersions = ProcessVersion::with('process')->get();

        /* @var ProcessVersion $processVersion */
        // Von der aktuellen in der Entwicklung befindlichen Version die Abhängigkeiten als "latest_dependencies" exportieren.
        foreach ($processVersions as $processVersion) {
            if($processVersion->version === $processVersion->process->latest_version) {
                $processVersion->exportDependencies(true);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $files =  Storage::allFiles(process_types_path());
        Storage::delete($files);
    }
};
