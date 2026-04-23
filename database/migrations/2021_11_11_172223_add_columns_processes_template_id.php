<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "template_id" Spalte zur "processes" Tabelle hinzu, in der festgehalten wird, ob ein Prozess auf Basis eines
 * anderen Prozesses erstellt wurde.
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
            $table->string('template_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('template_id');
        });
    }
};
