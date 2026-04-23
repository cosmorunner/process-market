<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if(!Schema::hasColumn('processes', 'deleted_at')) {
            Schema::table('processes', function(Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if(Schema::hasColumn('processes', 'deleted_at')) {
            Schema::table('processes', function(Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
