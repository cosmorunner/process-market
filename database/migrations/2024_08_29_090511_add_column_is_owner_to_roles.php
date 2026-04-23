<?php

use App\Models\Organisation;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up() {
        if (!Schema::hasColumn('roles', 'is_owner')) {
            Schema::table('roles', function(Blueprint $table) {
                $table->boolean('is_owner')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (!Schema::hasColumn('roles', 'is_owner')) {
            Schema::table('roles', function(Blueprint $table) {
                $table->removeColumn('is_owner');
            });
        }

    }
};
