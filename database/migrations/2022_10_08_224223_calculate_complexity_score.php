<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;

/**
 * Für jeden bestehenden Graph die Complexity Score berechnen.
 * Class AddColumnsSimulationsContext
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        ProcessVersion::all()->each(fn(ProcessVersion $processVersion) => $processVersion->updateComplexityScore());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $graphs = ProcessVersion::all();

        foreach ($graphs as $processVersion) {
            $processVersion->update([
                'complexity_score' => 0.0
            ]);
        }
    }
};
