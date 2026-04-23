<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Process;

return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        if(!Schema::hasColumn('processes', 'full_namespace')) {
            Schema::table('processes', function (Blueprint $table) {
                $table->string('full_namespace')->nullable();
            });
        }

        Process::all()->each(function (Process $process) {
            $process->update(['full_namespace' => $process->namespace . '/' . $process->identifier]);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        if(Schema::hasColumn('processes', 'full_namespace')) {
            Schema::table('processes', function(Blueprint $table) {
                $table->dropColumn('full_namespace');
            });
        }
    }
};
