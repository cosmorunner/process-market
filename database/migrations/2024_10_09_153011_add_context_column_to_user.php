<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the "context" column to the "users" table.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('users', 'context')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('context')->default(null)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        if (Schema::hasColumn('users', 'context')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('context');
            });
        }
    }
};