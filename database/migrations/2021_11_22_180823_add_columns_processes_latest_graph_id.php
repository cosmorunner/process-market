<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "latest_graph_id" Spalte zur "processes" Tabelle hinzu, in der festgehalten wird, was die Id des aktuellsten Graph ist.
 * Class AddColumnsSimulationsContext
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('processes', function (Blueprint $table) {
            $table->string('latest_graph_id')->nullable();
            $table->string('latest_published_graph_id')->nullable();
        });

        \App\Models\Process::all()->each(function (\App\Models\Process $process) {
            $latestGraph = $process->versions->firstWhere('published_at', '=', null);
            $latestPublishedVersion = $process->versions->where('published_at', '!=', null)->sortByDesc('published_at')->first();

            $process->update([
                'latest_graph_id' => $latestGraph?->id,
                'latest_published_graph_id' => $latestPublishedVersion?->id
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('latest_graph_id');
            $table->dropColumn('latest_published_graph_id');
        });
    }
};
