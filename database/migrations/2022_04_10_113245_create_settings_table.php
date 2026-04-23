<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSettingsTable
 */
return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::create('settings', function(Blueprint $table) {
            $table->uuid('id');
            $table->string('name');
            $table->json('value')->nullable();
            $table->uuid('owner_id')->index()->nullable();
            $table->string('owner_type')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('settings');
    }
};
