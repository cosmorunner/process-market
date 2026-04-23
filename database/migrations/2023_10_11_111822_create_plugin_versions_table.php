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
        Schema::create('plugin_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
            $table->json('data')->nullable();
            $table->string('version');
            $table->text('changelog')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('plugin_versions');
    }
};
