<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the column "default_user" to the environment, which defines, with which user the environment should start.
 * Class AddColumnsSimulationsContext
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::table('environments', function (Blueprint $table) {
            $table->uuid('default_user')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::table('environments', function (Blueprint $table) {
            $table->dropColumn('default_user');
        });
    }
};
