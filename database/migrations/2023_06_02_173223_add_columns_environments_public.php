<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "initial_action_type_id" Spalte zur "environments" Tabelle hinzu. Wird genutzt um zu bestimmen, mit welcher
 * Aktion der Prozess starten soll.
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
            $table->boolean('public')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('environments', function (Blueprint $table) {
            $table->dropColumn('public');
        });
    }
};
