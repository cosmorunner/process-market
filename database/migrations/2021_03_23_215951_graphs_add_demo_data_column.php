<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class GraphsAddDemoDataColumn
 */
return new class extends Migration {

    /**
     * Fügt das JSON demo_data Datenfeld hinzu.
     *
     * @return void
     */
    public function up() {
        Schema::table('graphs', function (Blueprint $table) {
            $table->json('demo_data')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('graphs', function (Blueprint $table) {
            $table->dropColumn('demo_data');
        });
    }
};
