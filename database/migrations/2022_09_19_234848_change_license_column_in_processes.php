<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Process;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Process::all()->each(function(Process $process) {
            $license = $process->license;

            if(is_string($license)) {
                $license = json_decode($license, true);
            }

            if (!empty($license)) {
                $process->update([
                    'license' => [$license]
                ]);
            }
        });

        Schema::table('processes', function(Blueprint $table) {
            $table->renameColumn('license', 'license_options')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Process::all()->each(function(Process $process) {
            $licenseOptions = $process->license_options;

            if (!empty($licenseOptions)) {
                $process->update([
                    'license_options' => $licenseOptions[0] ?? []
                ]);
            }
        });

        Schema::table('processes', function(Blueprint $table) {
            $table->renameColumn('license_options', 'license')->default('[]');
        });
    }
};
