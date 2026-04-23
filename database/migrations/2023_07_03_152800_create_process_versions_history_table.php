<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::create('process_versions_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('process_version_id')->references('id')->on('process_versions');
            $table->string('command')->nullable();
            $table->json('command_payload')->nullable();
            $table->json('calculated');
            $table->json('definition');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('process_versions_history');
    }
};
