<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fügt die Spalte "updated_at" zur "accesses" Tabelle hinzu.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('accesses', 'updated_at')) {
            Schema::table('accesses', function(Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasColumn('accesses', 'updated_at')) {
            Schema::table('accesses', function(Blueprint $table) {
                $table->removeColumn('updated_at');
            });
        }
    }
};
