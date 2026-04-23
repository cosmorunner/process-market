<?php

use Illuminate\Database\Migrations\Migration;
use App\ProcessType\Definition;
use App\Models\ProcessVersion;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        ProcessVersion::all()->each(function(ProcessVersion $processVersion) {
            $definition = $processVersion->definition->toArray();
            $dependencies = $processVersion->definition->dependencies;
            $dependencies['process_types'] = array_values(array_filter($dependencies['process_types'], fn(string $item) => Definition::validNamespace($item)));
            $definition['dependencies'] = $dependencies;

            $processVersion->definition = $definition;
            $processVersion->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

    }
};
