<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Umgenennung der Spalte "platform_url" in der Tabelle "users" zu "demo_identifier".
 * Class RenameColumnUsersPlatformUrlDemoIdentifier
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('platform_url', 'demo_identifier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('demo_identifier', 'platform_url');
        });
    }
};
