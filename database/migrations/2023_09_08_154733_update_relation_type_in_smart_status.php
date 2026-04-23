<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use App\Loaders\PipeLoader;

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
                $updated = false;

                foreach ($definition->statusTypes as $statusType) {
                    /* @var StatusType $statusType */

                    if (!empty($statusType->smart)) {
                        $options = $statusType->smart['options'];

                        if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                            $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['relation_type'], $definition);
                            if ($pipeNotation) {
                                $options['relation_type'] = $pipeNotation;
                            }
                        }

                        $statusType->smart['options'] = $options;
                        $updated = true;
                    }
                }
                if ($updated) {
                    $version->update(['definition' => $definition->toArray()]);

                    $version->exportDefinition();
                    $version->exportDefinition($version->definition->namespace . '_' . $version->definition->identifier . '@latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $definitionObject = new Definition($definition);
                $updated = false;

                foreach ($definition['status_types'] as $index => $statusType) {
                    if (!empty($statusType['smart'])) {
                        $options = $statusType['smart']['options'];

                        if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                            $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['relation_type'], $definitionObject);
                            if ($pipeNotation) {
                                $options['relation_type'] = $pipeNotation;
                            }
                        }

                        $definition['status_types'][$index]['smart']['options'] = $options;
                        $updated = true;
                    }
                }
                if ($updated) {
                    $history->update(['definition' => $definition]);
                }

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
                $updated = false;

                foreach ($definition->statusTypes as $statusType) {
                    /* @var StatusType $statusType */

                    if (!empty($statusType->smart)) {
                        $options = $statusType->smart['options'];

                        if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                            $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($options['relation_type'], $definition);
                            if ($pipeNotation) {
                                $options['relation_type'] = $pipeNotation;
                            }
                        }

                        $statusType->smart['options'] = $options;
                        $updated = true;
                    }
                }
                if ($updated) {
                    $version->update(['definition' => $definition->toArray()]);

                    $version->exportDefinition();
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $definitionObject = new Definition($definition);
                $updated = false;

                foreach ($definition['status_types'] as $index => $statusType) {
                    if (!empty($statusType['smart'])) {
                        $options = $statusType['smart']['options'];

                        if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                            $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($options['relation_type'], $definitionObject);
                            if ($pipeNotation) {
                                $options['relation_type'] = $pipeNotation;
                            }
                        }

                        $definition['status_types'][$index]['smart']['options'] = $options;
                        $updated = true;
                    }
                }
                if ($updated) {
                    $history->update(['definition' => $definition]);
                }
            }
        });
    }
};
