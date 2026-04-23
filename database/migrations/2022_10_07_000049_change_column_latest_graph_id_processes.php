<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        Schema::table('processes', function(Blueprint $table) {
            $table->renameColumn('latest_graph_id', 'latest_version_id');
        });

        Schema::table('processes', function(Blueprint $table) {
            $table->renameColumn('latest_published_graph_id', 'latest_published_version_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('processes', function(Blueprint $table) {
            $table->renameColumn('latest_version_id', 'latest_graph_id');
        });

        Schema::table('processes', function(Blueprint $table) {
            $table->renameColumn('latest_published_version_id', 'latest_published_graph_id');
        });
    }
};
