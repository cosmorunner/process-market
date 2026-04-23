<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('solutions', function(Blueprint $table) {
            $table->renameColumn('license', 'license_options')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('solutions', function(Blueprint $table) {
            $table->renameColumn('license_options', 'license')->default('[]');
        });
    }
};
