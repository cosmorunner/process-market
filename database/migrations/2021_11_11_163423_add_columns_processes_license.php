<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "license" Spalte zur "processes" Tabelle hinzu, in der die Lizenz-Optionen gespeichert sind, die Benutzer
 * erwerben können.
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
            $table->json('license')->default('{}');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('license');
        });
    }
};
