<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSimulationsTable
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('simulations', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->uuid('user_id')->nullable();
            $table->uuid('process_id')->nullable();
            $table->uuid('graph_id');
            $table->uuid('environment_id')->nullable();
            $table->text('token')->nullable();
            $table->uuid('allisa_id')->nullable();
            $table->uuid('allisa_process_type_id')->nullable();
            $table->uuid('allisa_user_id')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('simulations');
    }
};
