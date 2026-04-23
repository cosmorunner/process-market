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
        foreach ($processVersions as $processVersion) {
            // Prozess-Version in der Version als JSON-Datei exportieren inklusive Abhängigkeitsdatei.
            $processVersion->updateDependencies();
            $processVersion->exportDefinition();
            $processVersion->exportDependencies();

            if($processVersion->version === $processVersion->process->latestPublishedVersion?->version) {
                // Die aktuellste Prozess-Version als "latest"-Version exportieren inklusive der "latest" Abhängigkeitsdatei.
                $processVersion->exportDefinition($processVersion->definition->namespace . '_' . $processVersion->definition->identifier . '@latest');
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
