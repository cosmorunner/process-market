<?php

use App\Graph\Cytoscape;
use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Collection;

/**
 * Recalculates the graphs.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            foreach ($versions as $version) {
                $newCalculated = (new Cytoscape($version->definition))->transform();
                $currentCalculated = Cytoscape::applyOldPositions($newCalculated, $version->calculated);
                $version->update(['calculated' => $currentCalculated]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {

    }
};
