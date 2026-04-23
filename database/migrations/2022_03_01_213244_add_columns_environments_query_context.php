<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "query_context" Spalte zur "environments" Tabelle hinzu. Wird genutzt um zu bestimmen, mit welcher
 * URl "context" Parameter eine Initialaktion geöffent wird bei einer Demo.
 * Class AddColumnsSimulationsContext
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('environments', function (Blueprint $table) {
            $table->string('query_context')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('environments', function (Blueprint $table) {
            $table->dropColumn('query_context');
        });
    }
};
