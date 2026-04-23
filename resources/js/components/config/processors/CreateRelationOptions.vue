<template>
    <div>
        <MultipleExecution :multiple-value="(options.multiple || {}).value || ''" :outputs="outputs"
                           :action-type="actionType" @multiple-value-change="onMultipleValueChange"
                           :editable="ui.editable"/>
        <hr/>
        <AutocompleteSelector :items="options.to" :label="'Prozesse'" :icon="'grain'" :action-type="actionType"
                              :max-items="1" :outputs-from-actiontype-only="true"
                              :syntax-include="['action.outputs', 'process.outputs', 'reference.outputs', 'auth', 'users']"
                              :pipe-include="['action_type_processors']" :editable="ui.editable"
                              @items-changed="$emit('option-change', 'to', $event)"/>
        <AutocompleteSelector :items="options.relation_type ? [options.relation_type] : []" :label="'Verknüpfungstyp'"
                              :icon="'settings_ethernet'" :max-items="1"
                              :pipe-include="['relation_types', 'graphs_relation_types']"
                              @items-changed="onChangeRelationType($event, 'source_relation_type')"
                              :editable="ui.editable"/>
        <div
            v-if="Object.keys(options.data).length || (selectedRelationType && Object.keys(selectedRelationType.default).length)">
            <label class="mb-0">Verknüpfungsdaten</label>
            <div v-for="(dataValue, dataKey) in options.data || {}" class="row">
                <div class="col-4">
                    <button class="btn btn-block btn-outline-primary btn-sm dropdown-toggle" type="button"
                            :disabled="!ui.editable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>{{ dataKey }}</span>
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button type="button" class="dropdown-item"
                                v-for="newExernalOutputKey in usableRelationTypeDataKeys"
                                @click="onChangeKey(newExernalOutputKey, dataKey)">
                            <span>{{ newExernalOutputKey }}</span>
                        </button>
                    </div>
                </div>
                <div class="col-6">
                    <AutocompleteSelector :items="dataValue && dataValue.length ? [dataValue] : []"
                                          :action-type="actionType" :max-items="1" :add-only-from-autocomplete="false"
                                          :outputs-from-actiontype-only="true"
                                          :manual-items="[{label: 'Leere Zeichenkette', value: ''}]"
                                          :syntax-include="Object.keys(syntaxLoaderLabels())" :editable="ui.editable"
                                          @items-changed="onChangeValue($event, dataKey)"/>
                </div>
                <div class="col-1">
                    <button v-if="multipleOptions.value"
                            :class="'btn btn-sm btn-outline-primary ' + (extractFromArrayData.includes(dataKey) ? 'active' : '')"
                            @click="onToggleExtractArrayIndex(dataKey)" :disabled="!ui.editable" data-toggle="tooltip"
                            data-placement="top" title="Bei JSON-Arrays den Wert der Ausführungs-Iteration nutzen">
                        <span class="material-icons">read_more</span>
                    </button>
                </div>
                <div class="col-1">
                    <button class="float-right btn btn-sm btn-outline-danger" @click="onDeleteKey(dataKey)"
                            v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </div>
            <small class="d-block text-muted mb-2" v-if="Object.keys(options.data).length">
                <span>Datenfeld ➺ Wert</span>
            </small>
            <div class="d-flex justify-content-start" v-if="usableRelationTypeDataKeys.length && ui.editable">
                <button class="btn btn-sm btn-outline-success" @click="onAddData">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import MultipleExecution from "./MultipleExecution";
import OptionBadges from "../../utils/OptionBadges";
import {mapGetters, mapActions} from "vuex";
import SortedRelationTypes from "../../utils/SortedRelationTypes";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import {reduxActions} from "../../../store/develop-and-config";

export default {
    components: {
        AutocompleteSelector,
        SortedRelationTypes,
        OptionBadges,
        MultipleExecution
    },
    props: {
        options: Object,
        outputs: Object | Array,
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs',
            'graphs_relation_types',
            'relation_types'
        ]),
        usableRelationTypeDataKeys() {
            if (!this.selectedRelationType) {
                return [];
            }

            let relationTypes = [];

            // Get relation types from all process versions.
            if(this.selectedRelationType.namespace) {
                relationTypes = this.graphs_relation_types.filter(element => element.namespace === this.selectedRelationType.namespace && element.reference === this.selectedRelationType.reference);
            } else {
                relationTypes = this.relation_types.filter(element => element.reference === this.selectedRelationType.reference);
            }


            let relationTypeDataKeys = [];
            let optionsData = Object.keys(this.options.data);

            relationTypes.forEach(function (relationType) {
                Object.keys(relationType.default || {}).forEach(function (item) {
                    if (!relationTypeDataKeys.includes(item) && !optionsData.includes(item)) {
                        relationTypeDataKeys.push(item);
                    }
                });
            });

            return relationTypeDataKeys;
        },
        relationTypeReference() {
            if (this.options.relation_type) {
                return this.getSyntaxParts(this.options.relation_type).key;
            }

            return null;
        },
        selectedRelationType() {
            if (!this.options.relation_type) {
                return null;
            }

            let parts = this.getSyntaxParts(this.options.relation_type);

            if(parts.namespace) {
                return this.graphs_relation_types.find(ele => ele.reference === parts.key) || null;
            } else {
                return this.relation_types.find(ele => ele.reference === parts.key);
            }
        },
        multipleOptions() {
            return this.options.multiple || {};
        },
        extractFromArrayData() {
            return (this.multipleOptions.extract_from_array || {}).data || [];
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        itemsChanged(items) {
            this.$emit('option-change', 'to', items);
        },
        onMultipleValueChange(value) {
            if (!value) {
                this.updateExtractArrayIndex({});
            }

            this.$emit('option-change', 'multiple', {
                value: value,
                extract_from_array: {
                    data: []
                }
            });
        },
        onChangeKey(newExternalOutputKey, mappingKey) {
            let data = {...this.options.data};
            let value = data[mappingKey];
            delete data[mappingKey];

            this.$emit('option-change', 'data', {
                ...data,
                [newExternalOutputKey]: value
            });

            this.updateExtractArrayIndex(data);
        },
        onChangeValue(autoCompleteItems, key) {
            let value = autoCompleteItems.length ? autoCompleteItems[0] : '';

            this.$emit('option-change', 'data', {
                ...this.options.data,
                [key]: value
            });
        },
        onAddData() {
            if (!this.usableRelationTypeDataKeys.length) {
                return;
            }

            let nextKey = this.usableRelationTypeDataKeys[0];
            let defaultVal = this.selectedRelationType.default[nextKey] || '';

            this.$emit('option-change', 'data', {
                ...this.options.data,
                [this.usableRelationTypeDataKeys[0]]: defaultVal
            });
        },
        onDeleteKey(mappingKey) {
            let data = {...this.options.data};
            delete data[mappingKey];

            this.$emit('option-change', 'data', data);
            this.updateExtractArrayIndex(data);
        },
        onChangeRelationType(autocompleteItems) {
            if (!autocompleteItems.length) {
                this.$emit('option-change', 'relation_type', null);
                this.$emit('option-change', 'relation_type_name', null);

                return;
            }

            let parts = this.getSyntaxParts(autocompleteItems[0]);
            // Erst in external schauen
            let relationType = this.graphs_relation_types.find(ele => ele.namespace === parts.namespace && ele.reference === parts.key);

            // Wenn existiert dann Namespace aus parts übernehmen
            if (!relationType) {
                relationType = this.relation_types.find(ele => ele.reference === parts.key);
            }

            if (relationType) {
                this.$emit('option-change', 'relation_type', autocompleteItems[0]);
                this.$emit('option-change', 'relation_type_name', relationType.name);
            }
            else {
                this.$emit('option-change', 'relation_type', null);
                this.$emit('option-change', 'relation_type_name', null);
            }
        },
        onToggleExtractArrayIndex(item) {
            let multiple = {...this.multipleOptions};
            let extractFromArrayData = {...multiple.extract_from_array || {}}.data || [];

            if (extractFromArrayData.includes(item)) {
                extractFromArrayData = extractFromArrayData.filter(ele => ele !== item);
            }
            else {
                extractFromArrayData.push(item);
            }

            this.$emit('option-change', 'multiple', {
                ...multiple,
                extract_from_array: {
                    ...multiple.extract_from_array,
                    data: [...extractFromArrayData]
                }
            });
        },
        updateExtractArrayIndex(data) {
            // Wert aus "multiple.extract_from_array.data" entfernen
            let presentDataKeys = Object.keys(data);
            let extractFromArrayData = ({...this.multipleOptions.extract_from_array || {}}.data || []).filter(ele => presentDataKeys.includes(ele));

            this.$emit('option-change', 'multiple', {
                ...this.multipleOptions,
                extract_from_array: {
                    ...this.multipleOptions.extract_from_array,
                    data: [...extractFromArrayData]
                }
            });
        },
    }
};
</script>
