<?php

namespace Database\Seeders;

use App\Utils\RedisHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder {

    /**
     * Datenbank seeden mit "php artisan migrate:fresh --seed"
     * Alle Migrationen unter /database/migrations werden ausgeführt.
     * @param array|null $blueprint
     * @return void
     */
    public function run(array $blueprint = null) {
        // Erzeugte Dokumente und Templates entfernen.
        $whitelist = ['demo-bild.jpg', 'demo-dokument.pdf', 'demo-excel.xlsx'];
        $files = collect(Storage::files(null, true))->filter(fn($item) => !Str::contains($item, $whitelist));
        Storage::delete($files->toArray());

        // Standardgemäß nimmt der DatabaseSeeder den "default"-Blueprint.
        // Wird vom RunBlueprintSeed-Command genutzt.
        (new BlueprintSeeder())->run($blueprint ?? config('blueprints.default'));

        // Redis Cache Entfernen
        RedisHelper::flushAll();
    }
}
