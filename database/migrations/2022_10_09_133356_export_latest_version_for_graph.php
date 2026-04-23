<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Process;
use App\Models\ProcessVersion;

return new class extends Migration {

    /**
     * Exportiert für jeden aktuellste Version jedes Prozesses als "@latest"-Version.
     *
     * @return void
     */
    public function up() {
        Process::all()->each(function(Process $process) {
            $latest = $process->latestPublishedVersion;

            if ($latest instanceof ProcessVersion) {
                $fileName = $latest->definition->namespace . '_' . $latest->definition->identifier . '@latest';
                $latest->exportDefinition($fileName);
                $latest->exportDependencies(true);
            }
        });
    }

    /**
     * Alle Abhängigkeits-Dateien der fertiggestellten Graphen löschen.
     * @return void
     */
    public function down() {
        $filePaths = collect(Storage::files(config('app.process_types_dir'), false));
        $filtered = $filePaths->filter(fn(string $path) => Str::endsWith($path, '@latest.json'));

        Storage::delete($filtered->toArray());
    }
};
