<template>
    <div>
        <MultipleExecution :multiple-value="multipleOptions.value || ''"
                           :outputs="outputs"
                           :action-type="actionType"
                           :editable="ui.editable"
                           @multiple-value-change="onMultipleValueChange"
        />
        <hr/>
        <AutocompleteSelector
            :items="fullNamespace ? [fullNamespace] : []"
            :label="'Prozesstyp'"
            :icon="'local_offer'"
            :max-items="1"
            :pipe-include="['graphs']"
            :editable="ui.editable"
            @items-changed="fullNamespaceChanged"
        />
        <div class="form-group input-group-sm mb-2" v-if="options.process_type">
            <label class="mb-0" for="version">Version</label>
            <select class="form-control" id="version" name="version" :value="version" @change="versionChanged">
                <option v-for="version in versions" :value="version">
                    {{ version === 'latest' ? 'Aktuellste Version' : version }}
                </option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-2" v-if="options.process_type">
            <label class="mb-0" for="role">Benutzer-Rolle</label>
            <select class="form-control" id="role" name="role" :value="roleId" @change="roleChanged" :disabled="!ui.editable">
                <option value="">-</option>
                <option v-for="processTypeRole in processTypeRoles" :value="processTypeRole.id">{{
                        processTypeRole.name
                    }}
                </option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Name</label>
            <textarea class="form-control" rows="1"
                      @change="$emit('option-change', 'name', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.name"></textarea>
            <OptionBadgesWithText :value="options.name" display-block hide-on-empty/>
            <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array Syntax-Werten wird der Wert des
                Iterations-Indexes genutzt.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.outputs', 'action.outputs', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendName($event)"
            />
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Beschreibung</label>
            <textarea class="form-control" rows="1"
                      @change="$emit('option-change', 'description', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.description"></textarea>
            <OptionBadgesWithText :value="options.description" display-block hide-on-empty/>
            <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array Syntax-Werten wird der Wert des
                Iterations-Indexes genutzt.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.outputs', 'action.outputs', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendDescription($event)"
            />
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Referenz</label>
            <span class="px-0 py-1 d-block"><small class="text-danger material-icons">priority_high</small><small
                class="text-muted">Referenznamen von Prozess-Instanzen müssen pro Prozesstyp einzigartig sein.</small></span>
            <textarea class="form-control" rows="1"
                      @change="$emit('option-change', 'reference', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.reference"></textarea>
            <OptionBadgesWithText :value="options.reference" display-block hide-on-empty/>
            <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array Syntax-Werten wird der Wert des
                Iterations-Indexes genutzt.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.outputs', 'action.outputs', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendReference($event)"
            />
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="image">Icon</label>
            <div class="row no-gutters">
                <div class="col-6 pr-1">
                    <AutocompleteSelector
                        :items="image && image.length ? [options.image] : []"
                        :action-type="actionType"
                        :max-items="1"
                        :outputs-from-actiontype-only="true"
                        :add-only-from-autocomplete="false"
                        :syntax-include="['process.outputs', 'action.outputs']"
                        :editable="ui.editable"
                        @items-changed="$emit('option-change', 'image', $event.length ? $event[0] : '')"
                    />
                </div>
                <div class="col-6">
                    <IconSelection
                        v-if="image === null || (typeof image === 'string' && !image.startsWith('[[') && !image.includes('|'))"
                        :selected="image"
                        :require-selection="false"
                        :editable="ui.editable"
                        @on-select-icon="$emit('option-change', 'image', $event)"
                    />
                </div>
                <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array Syntax-Werten wird der Wert des
                    Iterations-Indexes genutzt.</small>
            </div>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="tags">Tags</label>
            <AutocompleteSelector
                :items="options.tags && options.tags.length ? [options.tags] : []"
                :action-type="actionType"
                :max-items="1"
                :outputs-from-actiontype-only="true"
                :add-only-from-autocomplete="false"
                :editable="ui.editable"
                :syntax-include="['process.outputs', 'action.outputs']"
                @items-changed="$emit('option-change', 'tags', $event.length ? $event[0] : '')"
            />
            <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array Syntax-Werten wird der Wert des
                Iterations-Indexes genutzt.</small>
        </div>
        <div class="form-group mb-3" v-if="processType">
            <label class="mb-0">Daten-Mapping</label>
            <div class="mb-1">
                <small class="text-muted">Prozess-Daten mit Aktions-Daten befüllen.</small>
                <small v-if="!processTypeOutputs.length" class="text-muted">Der ausgewählte Prozess besitzt keine Prozess-Daten.</small>
            </div>
            <template v-for="(actionOutput, externalOutputName) in options.mapping">
                <div class="row mb-2">
                    <div class="col-4">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary btn-sm dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span>{{ externalOutputName }}</span>
                            </button>
                            <div class="dropdown-menu scrollable-dropdown">
                                <button type="button" class="dropdown-item" v-for="newExernalOutputKey in usableOutputs"
                                        @click="onChangeOutputKeyMapping(newExernalOutputKey, externalOutputName)">
                                    <span>{{ newExernalOutputKey }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Wert..." :value="actionOutput"
                               @input="onInputOutputMapping($event, externalOutputName)"/>
                        <OptionBadgesWithText :value="actionOutput" display-block hide-on-empty/>
                    </div>
                    <div class="col-1">
                        <DropdownSelector
                            :action-type="actionType"
                            :outputs-from-actiontype-only="true"
                            :syntax-include="['process.outputs', 'action.outputs', 'process.meta', 'process.status', 'reference.outputs']"
                            @selected="onAppendOutputMappingValue($event, externalOutputName)"
                        />
                    </div>
                    <div class="col-1">
                        <button
                            v-if="multipleOptions.value"
                            :class="'btn btn-sm btn-outline-primary ' + (extractFromArrayMapping.includes(externalOutputName) ? 'active' : '')"
                            @click="onToggleExtractArrayIndex(externalOutputName)" :disabled="!ui.editable"
                            data-toggle="tooltip" data-placement="top"
                            title="Bei JSON-Arrays den Wert der Ausführungs-Iteration nutzen">
                            <span class="material-icons">read_more</span>
                        </button>
                    </div>
                    <div class="col-1">
                        <button class="float-right btn btn-sm btn-outline-danger" @click="onDeleteOutputMapping(externalOutputName)"
                                v-if="ui.editable">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </div>
            </template>
            <div class="d-flex justify-content-start" v-if="usableOutputs.length && actionType.outputs.length && ui.editable">
                <button class="btn btn-sm btn-outline-success" @click="onAddOutputMapping">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
        <div class="form-group input-group-sm mb-3">
            <label class="mb-0" for="output">Ausgabe</label>
            <select class="form-control" id="multiple" name="multiple" :value="options.output || ''" :disabled="!ui.editable"
                    @change="$emit('option-change', 'output', $event.target.value)">
                <option value="">Bitte wählen...</option>
                <option v-for="output in actionType.outputs" :value="output.name">
                    Aktions-Daten - {{ output.name }}
                </option>
            </select>
            <small class="text-muted">Die erzeugten Model-Pipe-Notationen der Prozess-Instanzen auf ein Aktions-Datenfeld schreiben.</small>
        </div>
    </div>
</template>

<script>

import AutocompleteProcessSelection from "./AutocompleteProcessSelection";
import MultipleExecution from "./MultipleExecution";
import utils from "../../../config-utils";
import {mapActions, mapGetters} from "vuex";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import IconSelection from "../../utils/IconSelection";
import {reduxActions} from "../../../store/develop-and-config";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";
import DropdownSelector from "../../utils/DropdownSelector";

export default {
    components: {
        DropdownSelector,
        OptionBadgesWithText,
        IconSelection,
        AutocompleteSelector,
        AutocompleteProcessSelection,
        MultipleExecution
    },
    data() {
        return {
            version: this.options.process_type ? this.options.process_type.split('@')[1] : '',
            fullNamespace: this.options.process_type ? this.options.process_type.split('@')[0] : null,
        };
    },
    props: {
        options: Object,
        actionType: Object,
        outputs: Object | Array
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs',
        ]),
        image() {
            return this.options.image;
        },
        roleId() {
            if (!this.options.role) {
                return '';
            }

            return this.getSyntaxParts(this.options.role).key;
        },
        processType() {
            return this.graphs.find(ele => ele.full_namespace === this.options.process_type) || null;
        },
        processTypeRoles() {
            // Falls der Prozess in den Graphen gefunden wurde, dessen Rollen zurückgeben. Ansonsten zumindest die
            // aktuelle Einstellung als Rolle angeben, sodass das Feld nicht leer erscheint.
            if (this.processType) {
                return this.processType.roles;
            }
            else if (this.options.role) {
                return [
                    {
                        id: this.options.role.split('|')[1],
                        name: this.options.role_name,
                    }
                ];
            }
            else {
                return [];
            }
        },
        processTypeOutputs() {
            return this.processType ? this.processType.outputs : [];
        },
        processTypeOutputsNames() {
            return this.processTypeOutputs.map(ele => ele.name);
        },
        usableOutputs() {
            return this.processTypeOutputsNames.filter(ele => !Object.keys(this.options.mapping).includes(ele));
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
                return versions.map(a => a.split('.').map(n => +n + 100000).join('.')).sort()
                    .map(a => a.split('.').map(n => +n - 100000).join('.')).reverse();
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
        fullNamespaceChanged(items) {
            let fullNamespace = null;

            if (items.length) {
                fullNamespace = items[0];
            }

            this.fullNamespace = fullNamespace;
            this.version = '';

            let processType = this.graphs.find(ele => (ele.namespace + '/' + ele.identifier) === fullNamespace) || null;

            if (processType) {
                this.version = 'latest';
            }

            this.$emit('option-change', 'role', '');
            this.$emit('option-change', 'mapping', {});
            this.$emit('option-change', 'process_type', fullNamespace && processType ? (fullNamespace + '@' + 'latest') : null);
        },
        versionChanged(e) {
            this.version = e.target.value;
            let mapping = {};
            let fullNamespace = this.fullNamespace + '@' + e.target.value;
            let processType = this.graphs.find(ele => ele.full_namespace === fullNamespace) || null;
            let role = processType.roles.find(ele => ele.id === (this.options.role || '')) || null;
            let processTypeOutputsNames = processType ? processType.outputs.map(ele => ele.name) : [];

            for (let outputName in this.options.mapping) {
                if (processTypeOutputsNames.includes(outputName)) {
                    mapping[outputName] = this.options.mapping[outputName];
                }
            }

            this.$emit('option-change', 'process_type', fullNamespace);
            this.$emit('option-change', 'role', role ? role.id : '');
            this.$emit('option-change', 'role_name', role ? role.name : '');
            this.$emit('option-change', 'mapping', mapping);
        },
        roleChanged(e) {
            let role = this.processTypeRoles.find(ele => ele.id === e.target.value);

            this.$emit('option-change', 'role', role ? 'role|' + role.id + '[Rolle - ' + role.name + ']' : null);
            this.$emit('option-change', 'role_name', role ? role.name : null);
        },
        onChangeOutputKeyMapping(newExternalOutputKey, oldExternalOutputKey) {
            let mapping = {...this.options.mapping};
            let value = mapping[oldExternalOutputKey];
            delete mapping[oldExternalOutputKey];

            this.$emit('option-change', 'mapping', {
                ...mapping,
                [newExternalOutputKey]: value
            });

            this.updateExtractArrayIndex(mapping);
        },
        onChangeOutputValueMapping(items, mappingKey) {
            let value = items.length ? items[0] : '';
            let mapping = {...this.options.mapping};

            this.$emit('option-change', 'mapping', {
                ...mapping,
                [mappingKey]: value
            });
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
        onInputOutputMapping(e, mappingKey) {
            let mapping = {...this.options.mapping};

            this.$emit('option-change', 'mapping', {
                ...mapping,
                [mappingKey]: e.target.value
            });
        },
        onAppendOutputMappingValue(e, mappingKey) {
            let mapping = {...this.options.mapping};
            let value = mapping[mappingKey] || '';

            this.$emit('option-change', 'mapping', {
                ...mapping,
                [mappingKey]: value + e.value_with_label
            });
        },
        setImage(items) {
            let value = items.length ? items[0] : '';
            this.$emit('option-change', 'image', value);
        },
        setConcat(optionName, value) {
            let current = [...this.options[optionName]];
            current[2] = value;

            this.$emit('option-change', optionName, current);
        },
        setOptionValue(items, name, index) {
            let value = items.length ? items[0] : '';
            let updatedOptions = [...this.options[name]];

            updatedOptions[index] = value;

            this.$emit('option-change', name, updatedOptions);
        },
        appendName(item) {
            let appended = this.options.name === null ? item.value_with_label : this.options.name + item.value_with_label;

            this.$emit('option-change', 'name', appended);
        },
        appendDescription(item) {
            let appended = this.options.description === null ? item.value_with_label : this.options.description + item.value_with_label;

            this.$emit('option-change', 'description', appended);
        },
        appendReference(item) {
            let appended = this.options.reference === null ? item.value_with_label : this.options.reference + item.value_with_label;

            this.$emit('option-change', 'reference', appended);
        },
    }
};
</script>
