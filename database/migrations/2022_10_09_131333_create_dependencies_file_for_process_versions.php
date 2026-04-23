<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Wenn ein Graph fertiggestellt wird, muss eine Abhängigkeits-Datei (JSON) erstellt werden, in der alle direkten und indirekten
 * Abhängigkeiten des Graphen notiert sind.
 */
return new class extends Migration {

    /**
     * Abhängigkeits-Dateien für alle fertiggestellten Graphen erstellen.
     * @return void
     */
    public function up() {
        ProcessVersion::published()->get()->each(fn(ProcessVersion $processVersion) => $processVersion->exportDependencies());
    }

    /**
     * Alle Abhängigkeits-Dateien der fertiggestellten Graphen löschen.
     * @return void
     */
    public function down() {
        $filePaths = collect(Storage::files(config('app.process_types_dir'), false));
        $filtered = $filePaths->filter(fn(string $path) => Str::endsWith($path, '_dependencies.json'));

        Storage::delete($filtered->toArray());
    }
};
