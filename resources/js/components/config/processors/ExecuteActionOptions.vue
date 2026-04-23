<template>
    <div>
        <MultipleExecution
            :multiple-value="(options.multiple || {}).value || ''"
            :outputs="outputs"
            :action-type="actionType"
            @multiple-value-change="onMultipleValueChange"
            :editable="ui.editable"
        />
        <hr/>
        <AutocompleteSelector
            :items="options.process ? [options.process] : []"
            :label="'Prozess'"
            :icon="'grain'"
            :action-type="actionType"
            :max-items="1"
            :outputs-from-actiontype-only="true"
            :syntax-include="['action.outputs', 'process.outputs', 'auth', 'users', 'reference.outputs', 'process.meta']"
            :pipe-include="['action_type_processors']"
            @items-changed="$emit('option-change', 'process', $event.length ? $event[0] : null)"
            :editable="ui.editable"
        />
        <small class="text-muted" v-if="multipleOptions.value">Bei Mehrfachausführung: Nur 1 Eintrag möglich. JSON-Array Wert erforderlich.</small>
        <AutocompleteSelector
            :items="fullNamespace ? [fullNamespace] : []"
            :label="'Prozesstyp'"
            :icon="'local_offer'"
            :max-items="1"
            :pipe-include="['graphs']"
            @items-changed="fullNamespaceChanged"
            :editable="ui.editable"
        />
        <div class="form-group input-group-sm mb-2" v-if="fullNamespace">
            <label class="mb-0" for="version">Version</label>
            <select class="form-control" id="version" name="version" :value="version" @change="versionChanged" :disabled="!ui.editable">
                <option v-for="item in versions" :value="item">
                    {{ item }}
                </option>
            </select>
        </div>
        <div class="alert alert-info p-2" role="alert" v-if="fullNamespace">
            Hinweis: Die Prozessoren "Aktion ausführen", "Prozess-Instanz erstellen", "Konnektor-Anfrage" und
            "Weiterleitung" werden bei Aktionen, die von einem Prozessor gestartet werden, nicht ausgeführt.
        </div>
        <div class="form-group input-group-sm mb-2" v-if="fullNamespace">
            <label class="mb-0" for="version">Aktionstyp</label>
            <select class="form-control" id="action_type" name="action_type" :value="actionTypeId" :disabled="!ui.editable" @change="actionTypeChanged">
                <option v-for="actionType in processTypeActionTypes" :value="actionType.id" :selected="actionTypeId === actionType.id">
                    {{ actionType.name }}
                </option>
            </select>
        </div>
        <div>
            <label class="mb-0">Daten-Mapping</label>
            <div class="mb-1">
                <small class="text-muted" v-if="selectedActionType && !(selectedActionType.outputs || []).length">Die
                    ausgewählte Aktion besitzt keine Aktions-Daten.</small>
                <small class="text-muted" v-else>Aktions-Daten in die ausführende Aktion übernehmen. Stellen Sie sicher, dass die Daten-Typen übereinstimmen.</small>
            </div>
            <template v-for="(actionOutput, externalActionOutputName) in options.mapping">
                <div class="row">
                    <div class="col-3">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" :disabled="!ui.editable"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span>{{ externalActionOutputName }}</span>
                            </button>
                            <div class="dropdown-menu scrollable-dropdown">
                                <button type="button" class="dropdown-item"
                                        v-for="newExernalOutputKey in usableOutputs"
                                        @click="onChangeOutputKeyMapping(newExernalOutputKey, externalActionOutputName)">
                                    <span>{{ newExernalOutputKey }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <textarea class="form-control" rows="2"
                                  @change="onChangeOutputValueMapping(externalActionOutputName, $event.target.value)"
                                  :readonly="!ui.editable" v-bind:value="actionOutput"></textarea>
                        <OptionBadgesWithText :value="options.mapping[externalActionOutputName]" display-block
                                              hide-on-empty/>
                    </div>
                    <div class="col-1 px-2">
                        <DropdownSelector :action-type="actionType" :dropdown-width="640"
                                          :outputs-from-actiontype-only="true"
                                          :syntax-include="Object.keys(syntaxLoaderLabels())" v-if="ui.editable"
                                          @selected="onAppendValueMapping($event, externalActionOutputName)"/>
                    </div>
                    <div class="col-1">
                        <button
                            v-if="multipleOptions.value"
                            :class="'btn btn-sm btn-outline-primary ' + (extractFromArrayMapping.includes(externalActionOutputName) ? 'active' : '')"
                            @click="onToggleExtractArrayIndex(externalActionOutputName)" :disabled="!ui.editable"
                            data-toggle="tooltip" data-placement="top"
                            title="Bei JSON-Arrays den Wert der Ausführungs-Iteration nutzen">
                            <span class="material-icons">read_more</span>
                        </button>
                    </div>
                    <div class="col-1">
                        <button class="float-right btn btn-sm btn-outline-danger" @click="onDeleteOutputMapping(externalActionOutputName)" v-if="ui.editable">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </div>
            </template>
            <small class="d-block text-muted mb-2" v-if="Object.keys(options.mapping).length">Ziel-Aktion Datensatz ← Wert</small>
            <div class="d-flex justify-content-start" v-if="usableOutputs.length && actionType.outputs.length && ui.editable">
                <button class="btn btn-sm btn-outline-success" @click="onAddOutputMapping">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import AutocompleteProcessSelection from "./AutocompleteProcessSelection";
import utils from "../../../config-utils";
import {mapActions, mapGetters} from "vuex";
import MultipleExecution from "./MultipleExecution";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import {reduxActions} from "../../../store/develop-and-config";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText.vue";
import DropdownSelector from "../../utils/DropdownSelector.vue";

export default {
    components: {
        DropdownSelector,
        OptionBadgesWithText,
        AutocompleteSelector,
        AutocompleteProcessSelection,
        MultipleExecution
    },
    props: {
        options: Object,
        outputs: Object | Array,
        actionType: Object,
    },
    data() {
        let fullNamespace = (this.options.action_type || '').split('::')[0] || '';

        return {
            version: this.options.action_type ? fullNamespace.split('@')[1] : 'latest',
            fullNamespace: this.options.action_type ? fullNamespace.split('@')[0] : null,
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'ui',
            'graphs',
            'relation_types_with_single_process',
            'graphs_output_names',
            'graphs_status_types'
        ]),
        processType() {
            return this.graphs.find(ele => ele.full_namespace === this.fullNamespace + '@' + this.version) || null;
        },
        processTypeOutputs() {
            return this.processType ? this.processType.outputs : [];
        },
        processTypeActionTypes() {
            // Falls der Prozess in den Graphen gefunden wurde, dessen Rollen zurückgeben. Ansonsten zumindest die
            // aktuelle Einstellung als Rolle angeben, sodass das Feld nicht leer erscheint.
            if (this.processType) {
                let actionTypes = [...this.processType.action_types]

                return actionTypes.sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1);
            }
            else if (this.options.action_type) {
                return [
                    {
                        id: this.options.action_type.split('|')[1],
                        name: this.options.action_type_name,
                    }
                ];
            }
            else {
                return [];
            }
        },
        selectedActionType() {
            return this.processTypeActionTypes.find(ele => ele.id === this.actionTypeId) || null;
        },
        actionTypeId() {
            if (!this.options.action_type) {
                return null;
            }

            return this.getSyntaxParts(this.options.action_type).key;
        },
        usableOutputs() {
            if (!this.selectedActionType) {
                return [];
            }

            return (this.selectedActionType.outputs || []).map(ele => ele.name).filter(ele => !Object.keys(this.options.mapping).includes(ele));
        },
        versions() {
            if (this.fullNamespace) {
                let namespace = this.fullNamespace.split('/')[0];
                let identifier = this.fullNamespace.split('/')[1];
                let versions = this.graphs.filter(ele => ele.namespace === namespace && ele.identifier === identifier)
                    .map(ele => ele.version);

                // Falls bereits eine Version zuvor angegeben wurde, wird diese hier manuell hinzugefügt.
                if (this.version) {
                    return [
                        ...new Set([
                            ...versions,
                            this.version
                        ])
                    ];
                }

                // Absteigend sortieren
                let numberVersions = versions.filter(ele => ele !== 'latest');

                return [
                    'latest',
                    ...numberVersions.map(a => a.split('.').map(n => +n + 100000).join('.')).sort()
                        .map(a => a.split('.').map(n => +n - 100000).join('.')).reverse()
                ];
            }
            else {
                return [];
            }
        },
        multipleOptions() {
            return this.options.multiple || {};
        },
        extractFromArrayMapping() {
            return (this.multipleOptions.extract_from_array || {}).mapping || [];
        }
    },
    methods: {
        ...mapActions(reduxActions),
        ...utils,
        onMultipleValueChange(value) {
            if (!value) {
                this.updateExtractArrayIndex({});
            }

            this.$emit('option-change', 'multiple', {
                value: value,
                extract_from_array: {
                    mapping: []
                }
            });
        },
        fullNamespaceChanged(items) {
            this.fullNamespace = items.length ? items[0] : null;
            this.version = '';


            let processType = this.graphs.find(ele => (ele.namespace + '/' + ele.identifier) === this.fullNamespace) || null;

            if (processType) {
                this.version = processType.version;
            }

            this.$emit('option-change', 'mapping', {});
            this.$emit('option-change', 'action_type', null);
            this.$emit('option-change', 'action_type_name', null);
        },
        versionChanged(e) {
            this.version = e.target.value;
            let namespace = this.fullNamespace + '@' + e.target.value;
            let processType = this.graphs.find(ele => ele.full_namespace === namespace);
            let actionType = this.selectedActionType ? this.selectedActionType : (processType ? (processType.action_types[0] || null) : null);
            let mapping = {};
            let processTypeOutputsNames = processType ? processType.outputs.map(ele => ele.name) : [];
            let actionTypeValue = null;

            for (let outputName in this.options.mapping) {
                if (processTypeOutputsNames.includes(outputName)) {
                    mapping[outputName] = this.options.mapping[outputName];
                }
            }

            if (actionType) {
                actionTypeValue = namespace + '::actionType|' + actionType.id;
            }

            this.$emit('option-change', 'mapping', mapping);
            this.$emit('option-change', 'action_type', actionTypeValue);
            this.$emit('option-change', 'action_type_name', actionType ? actionType.name : null);
        },
        actionTypeChanged(e) {
            let actionType = this.processTypeActionTypes.find(ele => ele.id === e.target.value);
            let actionTypeValue = null;

            if (actionType) {
                let fullNamespace = this.fullNamespace + '@' + this.version;
                actionTypeValue = fullNamespace + '::actionType|' + actionType.id + '[' + fullNamespace + ' - Aktion - ' + actionType.name + ']';
            }

            this.$emit('option-change', 'action_type', actionTypeValue);
            this.$emit('option-change', 'action_type_name', actionType ? actionType.name : null);
            this.$emit('option-change', 'mapping', {});
        },
        onChangeOutputKeyMapping(newExternalOutputKey, mappingKey) {
            let mapping = {...this.options.mapping};
            let value = mapping[mappingKey];
            delete mapping[mappingKey];

            this.$emit('option-change', 'mapping', {
                ...mapping,
                [newExternalOutputKey]: value
            });

            this.updateExtractArrayIndex(mapping);
        },
        onChangeOutputValueMapping(key, value) {
            let mapping = {...this.options.mapping};

            this.$emit('option-change', 'mapping', {
                ...mapping,
                [key]: value
            });
        },
        onAppendValueMapping(item, key) {
            let value = this.options.mapping.hasOwnProperty(key) ? this.options.mapping[key] + item.value_with_label : item.value_with_label;

            this.onChangeOutputValueMapping(key, value);
        },
        onAddOutputMapping() {
            if (!this.usableOutputs.length) {
                return;
            }
            this.$emit('option-change', 'mapping', {
                ...this.options.mapping,
                [this.usableOutputs[0]]: ''
            });
        },
        onDeleteOutputMapping(mappingKey) {
            let mapping = {...this.options.mapping};
            delete mapping[mappingKey];
            this.$emit('option-change', 'mapping', mapping);

            this.updateExtractArrayIndex(mapping);
        },
        onToggleExtractArrayIndex(item) {
            let multiple = {...this.multipleOptions};
            let extractFromArrayMapping = {...multiple.extract_from_array || {}}.mapping || [];

            if (extractFromArrayMapping.includes(item)) {
                extractFromArrayMapping = extractFromArrayMapping.filter(ele => ele !== item);
            }
            else {
                extractFromArrayMapping.push(item);
            }

            this.$emit('option-change', 'multiple', {
                ...multiple,
                extract_from_array: {
                    ...multiple.extract_from_array,
                     mapping: [...extractFromArrayMapping]
                }
            });
        },
        updateExtractArrayIndex(mapping) {
            // Wert aus "multiple.extract_from_array.mapping" entfernen
            let presentMappingKeys = Object.keys(mapping);
            let extractFromArrayMapping = ({...this.multipleOptions.extract_from_array || {}}.mapping || []).filter(ele => presentMappingKeys.includes(ele));

            this.$emit('option-change', 'multiple', {
                ...this.multipleOptions,
                extract_from_array: {
                    ...this.multipleOptions.extract_from_array,
                    mapping: [...extractFromArrayMapping]
                }
            });
        },
    }
};
</script>
