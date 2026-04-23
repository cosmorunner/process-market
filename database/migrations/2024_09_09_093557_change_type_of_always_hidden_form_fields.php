<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * This migration is used for the Jira ticket DEV-397.
 * It checks in all actiontype form components for fields, that have set the display rule to "always hidden".
 * It also checks for all sets, that are "always hidden" and have visible fields.
 * The field type of these fields is changed to input type "hidden".
 * Background: With the refactoring of the form component in DEV-397, some bugs have been fixed.
 * One bug was, that the value of a hidden field (by display rules) DID set their value in the action data
 * and DID send their value to the server. This is not intended. Regular "hidden" fields should not set their value in the action data,
 * that is send to the server. Only the value of input type "hidden" should send their value to the server.
 * To prevent any issues with the process flow, we change the field type to "hidden", so that
 * the value is nonetheless send to the server. In the FHP project "Beschaffungsprozess" this concerns many fields.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        $log = [];

        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) use (&$log) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $updatedDefinition = $this->upMethod($version->getRawDefintion(), $log);

                if (array_key_exists($version->full_namespace, $log) && !empty($log[$version->full_namespace])) {
                    $version->update(['definition' => $updatedDefinition]);
                    $version->exportDefinition();

                    // Export latest version
                    if ($version->process->latest_published_version_id === $version->id) {
                        $version->exportDefinition('latest');
                    }
                } else {
                    unset($log[$version->full_namespace]);
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $this->upMethod($history->getRawDefintion());
                $history->update(['definition' => $definition]);
            }
        });

        ksort($log);
        Storage::disk('logs')->put('dev-397-updated-fields.json', json_encode($log, JSON_PRETTY_PRINT));
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $this->downMethod($version->getRawDefintion());
                $version->update(['definition' => $definition]);
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $this->downMethod($history->getRawDefintion());
                $history->update(['definition' => $definition]);
            }
        });
    }

    /**
     * Up migration for both ProcessVersion and ProcessVersionHistory.
     * @param array $definition
     * @param array|null $log
     * @return array
     */
    private function upMethod(array $definition, array &$log = null) {
        $fullNamespace = $definition['namespace'] . '/' . $definition['identifier'] . '@' . $definition['version'];

        $log = is_null($log) ? [] : $log;
        $log[$fullNamespace] = [];

        $actionTypes = $definition['action_types'] ?? [];
        usort($actionTypes, fn($a, $b) => strcmp($a['name'], $b['name']));

        foreach ($actionTypes as $actionTypeKey => $actionType) {
            $actionTypeLogKey = $actionType['name'] . ' - ' . $actionType['reference'];
            $log[$fullNamespace][$actionTypeLogKey] = [];

            foreach ($actionType['components'] ?? [] as $componentKey => $component) {
                foreach ($component['options']['sets'] ?? [] as $setKey => $set) {
                    $hidden = $set['display']['hidden'] ?? [];

                    // First we check if the set as set "always hidden" with the display rules.
                    // We check for the fields in the set and change the type to hidden if the field has computed input and NO "hidden" display rules:
                    if (!empty($hidden) && ($hidden[0][0] ?? '') == 'always') {
                        foreach ($set['fields'] ?? [] as $fieldKey => $field) {

                            // If the field has a computed value, we set the type to "hidden" and set the migration-specific key "old_type"
                            // to be able to undo this operation in the "down" method of the migration
                            if (strlen($field['computed_input'] ?? '')) {
                                $log[$fullNamespace][$actionTypeLogKey]['sichtbare_felder_in_verstecktem_set'][] = $field['name'];
                                $field['old_type'] = $field['type'];
                                $field['type'] = 'hidden';
                                $set['fields'][$fieldKey] = $field;
                            }
                        }
                    }
                    // Second we check the visible sets (sets that are not ALWAYS hidden) for fields that are ALWAYS hidden.
                    // We check for the fields in the set and change the type to hidden if the field has computed input and NO "hidden" display rules:
                    else {
                        foreach ($set['fields'] ?? [] as $fieldKey => $field) {
                            $hidden = $field['display']['hidden'] ?? [];
                            $computed = trim($field['computed_input'] ?? '');

                            if (!empty($hidden) && ($hidden[0][0] ?? '') == 'always' && strlen($computed) && $field['type'] !== 'hidden') {
                                $log[$fullNamespace][$actionTypeLogKey]['versteckte_felder_in_sichtbarem_set'][] = $field['name'];
                                $field['old_type'] = $field['type'];
                                $field['type'] = 'hidden';
                                $set['fields'][$fieldKey] = $field;
                            }
                        }
                    }

                    $component['options']['sets'][$setKey] = $set;
                }

                $actionType['components'][$componentKey] = $component;
            }

            $actionTypes[$actionTypeKey] = $actionType;

            if (empty($log[$fullNamespace][$actionTypeLogKey]['sichtbare_felder_in_verstecktem_set'])) {
                unset($log[$fullNamespace][$actionTypeLogKey]['sichtbare_felder_in_verstecktem_set']);
            }

            if (empty($log[$fullNamespace][$actionTypeLogKey]['versteckte_felder_in_sichtbarem_set'])) {
                unset($log[$fullNamespace][$actionTypeLogKey]['versteckte_felder_in_sichtbarem_set']);
            }

            if (empty($log[$fullNamespace][$actionTypeLogKey])) {
                unset($log[$fullNamespace][$actionTypeLogKey]);
            }
        }

        $definition['action_types'] = $actionTypes;

        return $definition;
    }

    /**
     * Down migration for both ProcessVersion and ProcessVersionHistory.
     * @param array $definition
     * @return array
     */
    private function downMethod(array $definition) {
        $actionTypes = $definition['action_types'] ?? [];

        foreach ($actionTypes as $actionTypeKey => $actionType) {
            foreach ($actionType['components'] ?? [] as $componentKey => $component) {
                foreach ($component['options']['sets'] ?? [] as $setKey => $set) {
                    foreach ($set['fields'] ?? [] as $fieldKey => $field) {
                        if (array_key_exists('old_type', $field)) {
                            $field['type'] = $field['old_type'];
                            unset($field['old_type']);
                            $set['fields'][$fieldKey] = $field;
                        }
                    }

                    $component['options']['sets'][$setKey] = $set;
                }

                $actionType['components'][$componentKey] = $component;
            }

            $actionTypes[$actionTypeKey] = $actionType;
        }

        $definition['action_types'] = $actionTypes;

        return $definition;
    }
};
