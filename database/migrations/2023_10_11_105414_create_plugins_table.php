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
        Schema::create('plugins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type');
            $table->string('source');
            $table->uuid('creator_id');
            $table->string('namespace');
            $table->string('identifier');
            $table->boolean('enabled');
            $table->string('latest_version');
            $table->uuid('author_id')->index()->nullable();
            $table->string('author_type')->index()->nullable();
            $table->json('data')->nullable();
            $table->string('latest_version_id')->nullable();
            $table->string('latest_published_version_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('plugins');
    }
};
