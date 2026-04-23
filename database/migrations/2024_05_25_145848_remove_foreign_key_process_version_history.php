<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // SQLite does not support dropping foreign keys. Thats why we temporarily copy to "old" table, create new
        // history table and copy content.
        Schema::rename('process_versions_history', 'process_versions_history_old');

        Schema::create('process_versions_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('process_version_id');
            $table->string('command')->nullable();
            $table->json('command_payload')->nullable();
            $table->json('calculated');
            $table->json('definition');
            $table->timestamps();
        });

        DB::table('process_versions_history_old')->get()->each(function ($oldRecord) {
            DB::table('process_versions_history')->insert((array)$oldRecord);
        });

        Schema::drop('process_versions_history_old');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('process_versions_history', function(Blueprint $table) {
            $table->foreignUuid('process_version_id')->references('id')->on('process_versions');
        });
    }
};
