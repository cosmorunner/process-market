<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Umgenennung der Spalte "created_by" in der Tabelle "invitations" zu "creator_id".
 * Class RenameColumnCreatedByCreatorId
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('invitations', function (Blueprint $table) {
            $table->renameColumn('created_by', 'creator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('invitations', function (Blueprint $table) {
            $table->renameColumn('creator_id', 'created_by');
        });
    }
};
