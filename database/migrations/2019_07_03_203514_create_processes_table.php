<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProcessesTable
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('processes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('author_id');
            $table->text('description')->nullable();
            $table->string('namespace');
            $table->string('identifier');
            $table->unsignedInteger('visibility');
            $table->string('latest_version');
            $table->uuid('owner_id')->index()->nullable();
            $table->string('owner_type')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('processes');
    }
};
