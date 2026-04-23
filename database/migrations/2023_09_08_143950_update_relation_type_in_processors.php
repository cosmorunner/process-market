<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Definition;
use App\ProcessType\Processor;
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

                foreach ($definition->actionTypes as $actionType) {
                    foreach ($actionType->processors as $processor) {
                        /* @var Processor $processor */

                        $options = $processor->options;

                        if ($processor->identifier == 'create_relation' || $processor->identifier == 'delete_relation') {
                            if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                                $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['relation_type'], $definition);

                                if ($pipeNotation) {
                                    $options['relation_type'] = $pipeNotation;
                                }
                            }

                            $updated = true;
                            $processor->options = $options;
                        }

                        if ($processor->identifier == 'copy_relation') {
                            if (PipeLoader::hasValidAbtractModelFormat($options['source_relation_type'] ?? '')) {
                                $sourcePipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['source_relation_type'], $definition);

                                if ($sourcePipeNotation) {
                                    $options['source_relation_type'] = $sourcePipeNotation;
                                }
                            }

                            if (PipeLoader::hasValidAbtractModelFormat($options['target_relation_type'] ?? '')) {
                                $targetPipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['target_relation_type'], $definition);

                                if ($targetPipeNotation) {
                                    $options['target_relation_type'] = $targetPipeNotation;
                                }
                            }

                            $updated = true;
                            $processor->options = $options;
                        }

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

                foreach ($definition['action_types'] as $actionIndex => $actionType) {
                    foreach ($actionType['processors'] as $processorIndex => $processor) {
                        $options = $processor['options'];

                        if ($processor['identifier'] == 'create_relation' || $processor['identifier'] == 'delete_relation') {
                            if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                                $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['relation_type'], $definitionObject);
                                if ($pipeNotation) {
                                    $options['relation_type'] = $pipeNotation;
                                }
                            }

                            $updated = true;
                            $definition['action_types'][$actionIndex]['processors'][$processorIndex]['options'] = $options;
                        }

                        if ($processor['identifier'] == 'copy_relation') {
                            if (PipeLoader::hasValidAbtractModelFormat($options['source_relation_type'] ?? '')) {
                                $sourcePipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['source_relation_type'], $definitionObject);
                                if ($sourcePipeNotation) {
                                    $options['source_relation_type'] = $sourcePipeNotation;
                                }
                            }

                            if (PipeLoader::hasValidAbtractModelFormat($options['target_relation_type'] ?? '')) {
                                $targetPipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($options['target_relation_type'], $definitionObject);
                                if ($targetPipeNotation) {
                                    $options['target_relation_type'] = $targetPipeNotation;
                                }
                            }

                            $updated = true;
                            $definition['action_types'][$actionIndex]['processors'][$processorIndex]['options'] = $options;
                        }

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

                foreach ($definition->actionTypes as $actionType) {
                    foreach ($actionType->processors as $processor) {
                        /* @var Processor $processor */

                        $options = $processor->options;

                        if ($processor->identifier == 'create_relation' || $processor->identifier == 'delete_relation') {
                            if (PipeLoader::hasValidAbtractModelFormat($options['relation_type'] ?? '')) {
                                $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($options['relation_type'], $definition);
                                if ($pipeNotation) {
                                    $options['relation_type'] = $pipeNotation;
                                }
                            }

                            $updated = true;
                            $processor->options = $options;
                        }

                        if ($processor->identifier == 'copy_relation') {
                            if (PipeLoader::hasValidAbtractModelFormat($options['source_relation_type'] ?? '')) {
                                $sourcePipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($options['source_relation_type'], $definition);
                                if ($sourcePipeNotation) {
                                    $options['source_relation_type'] = $sourcePipeNotation;
                                }
                            }

                            if (PipeLoader::hasValidAbtractModelFormat($options['target_relation_type'] ?? '')) {
                                $targetPipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($options['target_relation_type'], $definition);
                                if ($targetPipeNotation) {
                                    $options['target_relation_type'] = $targetPipeNotation;
                                }
                            }

                            $updated = true;
                            $processor->options = $options;
                        }

                    }
                }
                if ($updated) {
                    $version->update(['definition' => $definition->toArray()]);

                    $version->exportDefinition();
                    $version->exportDefinition('latest');
                }
            }
        });
    }
};
