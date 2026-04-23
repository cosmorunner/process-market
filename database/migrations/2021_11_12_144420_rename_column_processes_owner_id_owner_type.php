<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Umgenennung der Spalte "owner_id" und "owner_type" in der Tabelle "processes" zu "author_id" and "author_type".
 * Class RenameColumnProcessesOwnerIdOwnerType
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('processes', function (Blueprint $table) {
            $table->renameColumn('owner_type', 'author_type')->nullable()->default('');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'author_id')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('processes', function (Blueprint $table) {
            $table->renameColumn('author_id', 'owner_id');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->renameColumn('author_type', 'owner_type');
        });
    }
};
