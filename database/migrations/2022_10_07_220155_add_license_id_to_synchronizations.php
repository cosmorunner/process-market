<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die "license_id" Spalte zur "synchronizations" Tabelle hinzu.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('synchronizations', function(Blueprint $table) {
            $table->uuid('license_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('synchronizations', function(Blueprint $table) {
            $table->removeColumn('license_id');
        });
    }
};
