<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die Spalte "license_id" zur "demos" Tabelle hinzu.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('demos', function(Blueprint $table) {
            $table->uuid('license_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('demos', function(Blueprint $table) {
            $table->removeColumn('license_id');
        });
    }
};
