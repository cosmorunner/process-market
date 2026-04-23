<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateInvitationsTable
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::create('invitations', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->uuid('resource_id')->nullable();
            $table->string('resource_type')->nullable();
            $table->uuid('role_id')->nullable();
            $table->uuid('created_by');
            $table->timestamp('created_at', 0);
            $table->timestamp('expires_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('invitations');
    }
};
