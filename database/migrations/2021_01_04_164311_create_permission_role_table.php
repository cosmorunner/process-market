<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreatePermissionRoleTable
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::create('permission_role', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('permission_id');
            $table->uuid('role_id');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('permission_role');
    }
};
