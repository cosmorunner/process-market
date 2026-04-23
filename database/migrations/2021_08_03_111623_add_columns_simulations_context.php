<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "context" Spalte zur "simulations" Tabelle hinzu. Diese wird genutzt um URL GET Query Parameter "context"
 * zu speichern, welcher bei Inititalaktionen genutzt werden kann.
 * Class AddColumnsSimulationsContext
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('simulations', function (Blueprint $table) {
            $table->string('context')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('simulations', function (Blueprint $table) {
            $table->dropColumn('context');
        });
    }
};
