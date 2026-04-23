<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "complexity_score" Spalte zur "graphs" Tabelle hinzu.
 * Class AddColumnsSimulationsContext
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('graphs', function (Blueprint $table) {
            $table->decimal('complexity_score', 8, 1)->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('graphs', function (Blueprint $table) {
            $table->dropColumn('complexity_score');
        });
    }
};
