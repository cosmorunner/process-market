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
        Schema::create('solution_versions', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('solution_id');
            $table->json('data');
            $table->string('version');
            $table->text('changelog')->nullable();
            $table->decimal('complexity_score', 8, 1)->default(0.0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('solution_versions');
    }
};
