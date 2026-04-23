<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * In "environments" die Spalte "graph_id" in "process_version_id" umbenennen.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('environments', function(Blueprint $table) {
            $table->renameColumn('graph_id', 'process_version_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('environments', function(Blueprint $table) {
            $table->renameColumn('process_version_id', 'graph_id');
        });
    }
};
