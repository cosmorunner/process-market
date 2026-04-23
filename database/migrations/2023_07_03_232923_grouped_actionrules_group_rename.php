<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Collection;
use App\ProcessType\ActionRule;

/**
 * Recalculates the graphs.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;

                foreach ($definition->actionTypes as $actionType) {
                    /* @var ActionRule $actionRule */
                    foreach ($actionType->actionRules as $actionRule) {
                        $actionRule->group = 'group_1';
                    }
                }

                $version->update(['definition' => $definition->toArray()]);

                $version->exportDefinition();
                $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;

                foreach ($definition->actionTypes as $actionType) {
                    /* @var ActionRule $actionRule */
                    foreach ($actionType->actionRules as $actionRule) {
                        $actionRule->group = 'default';
                    }
                }

                $version->update(['definition' => $definition->toArray()]);

                $version->exportDefinition();
                $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
            }
        });
    }
};
