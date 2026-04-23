<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Definition;
use App\ProcessType\ListConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

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

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    // Only these two templates depend on relation types.
                    if (!in_array($listConfig->template, ['process_relations', 'process_identity_relations'])) {
                        continue;
                    }
                    $updated = true;

                    $data = $listConfig->data;

                    $data['source']['select'] = array_values(array_filter($data['source']['select'], function ($value) {
                        return $value !== 'relations.single as relations_single';
                    }));

                    $data['columns'] = array_values(array_filter($data['columns'], function ($value) {
                        return $value['data'] !== 'relations_single';
                    }));

                    $whereIn = $data['source']['whereIn'] ?? [];

                    foreach ($whereIn as $conditionIndex => $condition) {
                        if ($condition[0] == 'relations.relation_type_id') {

                            foreach ($condition[1] as $index => $item) {
                                $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($item, $definition, false);

                                if ($pipeNotation) {
                                    // For internal relation types, a syntax loader is added as namespace to the pipe notation.
                                    if (!str_contains($pipeNotation, '::')) {
                                        $pipeNotation = '[[process.process_type.full_namespace]]::' . $pipeNotation;
                                    }
                                    $whereIn[$conditionIndex][1][$index] = $pipeNotation;
                                }
                            }

                            $whereIn[$conditionIndex][0] = 'relations.relation_type_pipe_notation';
                        }
                    }

                    $data['source']['whereIn'] = $whereIn;
                    $listConfig->data = $data;
                }

                // If a list config is updated.
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

                foreach ($definition['list_configs'] as $key => $listConfig) {
                    // Only these two templates depend on relation types.
                    if (!in_array($listConfig['template'], ['process_relations', 'process_identity_relations'])) {
                        continue;
                    }
                    $updated = true;

                    $data = $listConfig['data'];

                    $data['source']['select'] = array_values(array_filter($data['source']['select'], function ($value) {
                        return $value !== 'relations.single as relations_single';
                    }));

                    $data['columns'] = array_values(array_filter($data['columns'], function ($value) {
                        return $value['data'] !== 'relations_single';
                    }));

                    $listConfig['data_aliases'] = array_values(array_filter($listConfig['data_aliases'], function ($value) {
                        return $value !== 'relations_single';
                    }));

                    $whereIn = $data['source']['whereIn'] ?? [];

                    foreach ($whereIn as $conditionIndex => $condition) {

                        if ($condition[0] == 'relations.relation_type_id') {

                            foreach ($condition[1] as $index => $item) {
                                $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($item, $definitionObject, false);

                                if ($pipeNotation) {
                                    // For internal relation types, a syntax loader is added as namespace to the pipe notation.
                                    if (!str_contains($pipeNotation, '::')) {
                                        $pipeNotation = '[[process.process_type.full_namespace]]::' . $pipeNotation;
                                    }
                                    $whereIn[$conditionIndex][1][$index] = $pipeNotation;
                                }
                            }
                            $whereIn[$conditionIndex][0] = 'relations.relation_type_pipe_notation';
                        }
                    }

                    $data['source']['whereIn'] = $whereIn;
                    $listConfig['data'] = $data;
                    $definition['list_configs'][$key] = $listConfig;
                }
                // If a list config is updated.
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

                foreach ($definition->listConfigs as $listConfig) {
                    /* @var ListConfig $listConfig */

                    // Only these two templates depend on relation types.
                    if (!in_array($listConfig->template, ['process_relations', 'process_identity_relations'])) {
                        continue;
                    }
                    $updated = true;

                    $data = $listConfig->data;
                    $whereIn = $data['source']['whereIn'] ?? [];

                    foreach ($whereIn as $conditionIndex => $condition) {
                        if ($condition[0] == 'relations.relation_type_pipe_notation') {
                            foreach ($condition[1] as $index => $item) {
                                // For internal relation types, a syntax loader is added as namespace to the pipe notation.
                                $item = str_replace('[[process.process_type.full_namespace]]::', '', $item);
                                $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($item, $definition);
                                if ($pipeNotation) {
                                    $whereIn[$conditionIndex][1][$index] = $pipeNotation;
                                }
                            }
                            $whereIn[$conditionIndex][0] = 'relations.relation_type_id';
                        }
                    }

                    $data['source']['whereIn'] = $whereIn;
                    $listConfig->data = $data;
                }

                // If a list config is updated.
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

                foreach ($definition['list_configs'] as $key => $listConfig) {
                    // Only these two templates depend on relation types.
                    if (!in_array($listConfig['template'], ['process_relations', 'process_identity_relations'])) {
                        continue;
                    }
                    $updated = true;

                    $data = $listConfig['data'];
                    $whereIn = $data['source']['whereIn'] ?? [];

                    foreach ($whereIn as $conditionIndex => $condition) {
                        if ($condition[0] == 'relations.relation_type_pipe_notation') {
                            foreach ($condition[1] as $index => $item) {
                                // For internal relation types, a syntax loader is added as namespace to the pipe notation.
                                $item = str_replace('[[process.process_type.full_namespace]]::', '', $item);
                                $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($item, $definitionObject);
                                if ($pipeNotation) {
                                    $whereIn[$conditionIndex][1][$index] = $pipeNotation;
                                }
                            }
                            $whereIn[$conditionIndex][0] = 'relations.relation_type_id';
                        }
                    }

                    $data['source']['whereIn'] = $whereIn;
                    $listConfig['data'] = $data;
                    $definition['list_configs'][$key] = $listConfig;
                }
                // If a list config is updated.
                if ($updated) {
                    $history->update(['definition' => $definition]);

                }
            }
        });
    }
};
