<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder für eine Blueprint-Konfiguration
 * Class BlueprintSeeder
 * @package Database\Seeders
 */
class BlueprintSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @param array|null $blueprint
     * @return void
     */
    public function run(array $blueprint = null) {
        // Standardgemäß nimmt der DatabaseSeeder den "default"-Blueprint.
        // Wird vom RunBlueprintSeed-Command genutzt.
        $blueprint = $blueprint ?? config('blueprints.default');

        $this->callWith(UsersTableSeeder::class, [$blueprint]);
    }
}
