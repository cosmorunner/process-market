<?php

use App\Graph\Cytoscape;
use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use Illuminate\Support\Collection;
use App\ProcessType\StatusRule;

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
                    /* @var StatusRule $statusRule */
                    foreach ($actionType->statusRules as $statusRule) {
                        $statusRule->conditions = array_map(function ($item) {
                            return [$item[0], 'group_1', $item[1], $item[2], $item[3]];
                        }, $statusRule->conditions);
                    }
                }

                $newCalculated = (new Cytoscape($definition))->transform();
                $currentCalculated = Cytoscape::applyOldPositions($newCalculated, $version->calculated);
                $version->update([
                    'calculated' => $currentCalculated,
                    'definition' => $definition->toArray()
                ]);

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
                    /* @var StatusRule $statusRule */
                    foreach ($actionType->statusRules as $statusRule) {
                        $statusRule->conditions = array_map(function ($item) {
                            return [$item[0], $item[2], $item[3], $item[4]];
                        }, $statusRule->conditions);
                    }
                }

                $newCalculated = (new Cytoscape($definition))->transform();
                $currentCalculated = Cytoscape::applyOldPositions($newCalculated, $version->calculated);
                $version->update([
                    'calculated' => $currentCalculated,
                    'definition' => $definition->toArray()
                ]);

                $version->exportDefinition();
                $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
            }
        });
    }
};
