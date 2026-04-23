<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Solution;

return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::table('solutions', function (Blueprint $table) {
            $table->string('full_namespace')->nullable();
        });

        Solution::all()->each(function (Solution $solution) {
            $solution->update(['full_namespace' => $solution->namespace . '/' . $solution->identifier]);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::table('solutions', function (Blueprint $table) {
            $table->dropColumn('full_namespace');
        });
    }
};
