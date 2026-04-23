<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProcessVersion;

return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::all()->each(function (ProcessVersion $processVersion) {
            $processVersion->update(['full_namespace' => $processVersion->definition->fullNamespaceWithVersion()]);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {

    }
};
