<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSynchronizationsTable
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('synchronizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('system_id')->index();
            $table->uuid('user_id');
            $table->uuid('subject_id')->index();
            $table->string('subject_type')->index();
            $table->integer('response_code');
            $table->string('response_message');
            $table->timestamp('created_at', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('synchronizations');
    }
};
