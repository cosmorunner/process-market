<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Definition;
use App\ProcessType\Listener;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $updated = false;

                foreach ($definition->listeners as $listener) {
                    /* @var Listener $listener */

                    if (!empty($listener->relation_type)) {
                        $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($listener->relation_type, $definition);
                        if ($pipeNotation) {
                            $listener->relation_type = $pipeNotation;
                        }
                        $updated = true;
                    }
                }
                if ($updated) {
                    $version->update(['definition' => $definition->toArray()]);

                    $version->exportDefinition();

                    if ($version->process->latest_published_version_id === $version->id) {
                        $version->exportDefinition('latest');
                    }
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $definitionObject = new Definition($definition);
                $updated = false;

                foreach ($definition['listeners'] as $index => $listener) {
                    if (!empty($listener['relation_type'])) {
                        $pipeNotation = replace_id_with_reference_in_relation_type_pipe_notation($listener['relation_type'], $definitionObject);
                        if ($pipeNotation) {
                            $definition['listeners'][$index]['relation_type'] = $pipeNotation;
                            $updated = true;
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

                foreach ($definition->listeners as $listener) {
                    /* @var Listener $listener */

                    if (!empty($listener->relation_type)) {
                        $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($listener->relation_type, $definition);
                        if ($pipeNotation) {
                            $listener->relation_type = $pipeNotation;
                        }
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

                foreach ($definition['listeners'] as $index => $listener) {
                    if (!empty($listener['relation_type'])) {
                        $pipeNotation = replace_reference_with_id_in_relation_type_pipe_notation($listener['relation_type'], $definitionObject);
                        if ($pipeNotation) {
                            $definition['listeners'][$index]['relation_type'] = $pipeNotation;
                            $updated = true;
                        }
                    }
                }
                if ($updated) {
                    $history->update(['definition' => $definition]);
                }
            }
        });
    }
};
