<template>
    <div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Standard-Rollenzuordnung</span>
            </div>
            <div class="card-body px-2 py-1">
                <span class="text-muted p-2 d-inline-block">Die Rolle mit der ein Benutzer Zugriff auf eine neu erstellte Prozess-Instanz erhält, sofern dem Benutzer nicht explizit Zugriff erteilt wird.</span>
                <select class="form-control form-control-sm mt-2" id="default_role_id" :value="defaultRoleId"
                        @change="patchDefinition('UpdateDefaultRole', {default_role_id: $event.target.value}, false)"
                        :disabled="!ui.editable">
                    <option :value="''">Keine Standard-Rollenzuordnung</option>
                    <option :value="role.id" v-for="role in roles">
                        {{ role.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Öffentliche Rolle</span>
            </div>
            <div class="card-body px-2 py-1">
                <span class="text-muted p-2 d-inline-block">Die Rolle die alle Benutzer in einer Prozess-Instanz einnehmen, unabhängig davon, ob sie explizit Zugriff erhalten haben oder nicht.</span>
                <select class="form-control form-control-sm mt-2" id="public_role_id" :value="publicRoleId"
                        @change="patchDefinition('UpdatePublicRole', {public_role_id: $event.target.value}, false)"
                        :disabled="!ui.editable">
                    <option :value="''">Keine öffentliche Rolle</option>
                    <option :value="role.id" v-for="role in roles">
                        {{ role.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Liste für Prozessverlauf</span>
            </div>
            <div class="card-body px-2 py-1">
                <span class="text-muted p-2 d-inline-block">Der Prozessverlauf wird unterhalb der Aktionsübersicht angezeigt. Standardgemäß ist es eine Liste aller ausgeführten Aktionen. Prozessrollen-Recht "Verlauf einsehen" erforderlich.</span>
                <select class="form-control form-control-sm mt-2" id="history_list_config_slug"
                        :value="historyListConfigSlug"
                        @change="patchDefinition('UpdateHistoryListConfig', {history_list_config_slug: $event.target.value}, false)"
                        :disabled="!ui.editable">
                    <option :value="''">Standard</option>
                    <option :value="listConfig.slug" v-for="listConfig in sortedListConfigs">
                        {{ listConfig.name }} - {{ listConfig.slug }}
                    </option>
                </select>
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Icon</span>
            </div>
            <div class="card-body px-2 py-1">
                <span
                    class="text-muted p-2 d-inline-block">Neue Prozess-Instanzen erhalten automatisch dieses Icon.</span>
                <IconSelection :require-selection="true" :selected="image"
                               :editable="ui.editable"
                               @on-select-icon="patchDefinition('UpdateImage', {image: $event}, false)"
                />
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Automatische Referenzerzeugung</span>
            </div>
            <div class="card-body px-2 py-1">
                <span class="text-muted px-2 pt-0 pb-0 d-inline-block">Beim Erstellen einer Prozess-Instanz wird dieses Muster für die Prozess-Referenz genutzt.</span>
                <span class="p-2 d-inline-block"><small class="text-danger material-icons">priority_high</small><small
                    class="text-muted">
                    Referenznamen von Prozess-Instanzen müssen pro Prozesstyp einzigartig sein.
                </small></span>
                <textarea class="form-control" rows="2" :value="referencePattern" :readonly="!ui.editable"
                          @change="onManualReferenceChange"></textarea>
                <div class="bg-light p-2" v-if="referencePattern">
                    <OptionBadgesWithText :value="referencePattern"/>
                </div>
                <DropdownSelector class="mt-2" :label="'Prozess-Datensatz'"
                                  :syntax-include="['process.outputs', 'date', 'graphs.meta', 'faker', 'system', 'variables']"
                                  v-if="ui.editable" @selected="concatReference"/>
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Duplikatsprüfung bei Prozess-Instanz Erstellung</span>
            </div>
            <div class="card-body px-2 py-1">
                <span class="text-muted p-2 d-inline-block">Beim Erstellen einer Prozess-Instanz wird eine Duplikatsprüfung auf Grundlage dieser Parameter erfolgen.</span>
                <div class="bg-light p-2">
                    <AutocompleteSelector label='Metadaten' icon="add" :items=uniqueRulesMetaData
                                          :manual-items="calculatedUniqueRuleItemsMetaData"
                                          @items-changed="uniqueProcessDatachanged('meta_data', $event)"/>
                </div>
                <div class="bg-light p-2">
                    <AutocompleteSelector label='Prozessdaten' icon="add" :items=uniqueRulesProcessData
                                          :manual-items="calculatedUniqueItemsProcessData"
                                          @items-changed="uniqueProcessDatachanged('process_data', $event)"/>
                </div>
            </div>
        </div>
        <div class="p-2">
            <Docs article="rules-options#reference"/>
        </div>
    </div>
</template>

<script>

import utils from '../../develop-utils';
import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import IconSelection from "../utils/IconSelection";
import DropdownSelector from "../utils/DropdownSelector";
import OptionBadgesWithText from "../utils/OptionBadgesWithText";
import Docs from "../utils/Docs";
import AutocompleteSelector from "../utils/AutocompleteSelector.vue";

export default {
    components: {
        AutocompleteSelector,
        Docs,
        OptionBadgesWithText,
        DropdownSelector,
        IconSelection
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
            'action_types',
            'outputs',
            'roles',
            'list_configs'
        ]),
        defaultRoleId() {
            return this.definition.default_role_id || '';
        },
        publicRoleId() {
            return this.definition.public_role_id || '';
        },
        historyListConfigSlug() {
            return this.definition.history_list_config_slug || '';
        },
        referencePattern() {
            return this.definition.reference_pattern || '';
        },
        uniqueRulesProcessData() {
            return this.definition.unique['process_data'] || [];
        },
        uniqueRulesMetaData() {
            return this.definition.unique['meta_data'] || [];
        },
        sortedListConfigs() {
            return [...this.list_configs].filter(ele => ele.data.source_type === 'sql_actions').sort((a, b) => a.name > b.name ? 1 : -1);
        },
        calculatedUniqueItemsProcessData() {
            let values = [];
            this.outputs.forEach(function (output) {
                values.push({
                    label: output.name,
                    value: output.name
                });
            });
            return values;
        },
        calculatedUniqueRuleItemsMetaData() {
            return [
                {
                    label: 'Name',
                    value: 'name'
                },
                {
                    label: 'Beschreibung',
                    value: 'description'
                },
                {
                    label: 'Referenz',
                    value: 'reference'
                }
            ];
        },
        image() {
            return this.definition.image || 'star';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onManualReferenceChange(e) {
            this.patchDefinition('UpdateReferencePattern', {
                reference_pattern: e.target.value
            }, false).catch(() => {
            });
        },
        concatReference(item) {
            this.patchDefinition('UpdateReferencePattern', {
                reference_pattern: this.referencePattern + item.value_with_label
            }, false).catch(() => {
            });
        },
        uniqueProcessDatachanged(type, items) {
            this.patchDefinition('UpdateProcessTypeUnique', {
                type: type,
                data: items ?? []
            }, false).catch(() => {
            });
        },
    }
};
</script>
