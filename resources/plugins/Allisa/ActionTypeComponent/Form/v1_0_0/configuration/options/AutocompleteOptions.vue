<template>
    <div>
        <div class="form-group mb-2">
            <label for="entries" class="mb-0">Max. Einträge</label>
            <input type="number" class="form-control form-control-sm" id="entries" v-model="field.max_items" min="1"
                   step="1" aria-describedby="entries" @input="onMaxItemsInput" :readonly="!editable"/>
            <small class="text-muted">Leer lassen für beliebig viele Einträge.</small>
        </div>
        <div class="mb-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="asString" @click="toggleAsString"
                       :checked="asString" :disabled="!editable">
                <label class="custom-control-label" for="asString">Werte als Semikolon konkatenierte Zeichenkette.
                    Standardgemäß wird ein JSON-Array zurückgegeben.</label>
            </div>
        </div>
        <div class="my-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="customValuesSwitch"
                       @click="toggleAllowCustomValues" :checked="allowCustomValues" :disabled="!editable">
                <label class="custom-control-label" for="customValuesSwitch">Eigene Werte erlauben?</label>
            </div>
        </div>
        <ItemsOptions :field="field" @property-change="onPropertyChange" :editable="editable"/>
        <ListConfigOptions :field="field" @property-change="onPropertyChange" :editable="editable"
                           :enable-search-option="false" :input-output-names="inputOutputNames"
                           @updated-list-config-column-aliases="onUpdateListColumnAliases($event)"/>
        <VariableOptions :field="field" @property-change="onPropertyChange" :editable="editable"/>

        <template v-if="listConfigColumnAliases.length && field.max_items === 1">
            <label class="mb-0">Mapping</label>
            <small class="text-muted d-block">Weitere Werte der gewählten Listenzeile auf Felder übernehmen. Nur
                möglich, wenn max. Einträge = 1.</small>
            <table class="table table-sm table-borderless mt-2 mb-0" v-if="Object.keys(field.mapping || {}).length">
                <thead>
                <tr>
                    <th class="font-weight-normal w-30 border-bottom">Feld</th>
                    <th class="font-weight-normal w-30 border-bottom">Wert 1</th>
                    <th class="font-weight-normal w-30 border-bottom">Wert 2</th>
                    <th class="font-weight-normal w-10 border-bottom"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(columns, fieldName) in (field.mapping || {})">
                    <td class="py-1 w-30 mb-1 text-truncate" :title="fieldName">
                        <span>{{ fieldName }}</span>
                    </td>
                    <td class="py-1 w-30 mb-1">
                        <span>{{ columns[0] || '' }}</span>
                    </td>
                    <td class="py-1 w-30 mb-1">
                        <span>{{ columns[1] || '' }}</span>
                    </td>
                    <td class="py-1 w-10 mb-1">
                        <button class="btn btn-sm btn-light float-right" @click="deleteMapping(fieldName)"
                                v-if="editable">
                            <span class="material-icons text-danger">close</span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="input-group input-group-sm mt-2" v-if="Object.keys(unusedInputOutputNames).length && editable">
                <div class="input-group-prepend input-group-sm">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        {{ newOption.field || 'Feld wählen' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button v-for="name in unusedInputOutputNames" type="button" class="dropdown-item"
                                @click="newOption.field = name">
                            {{ name }}
                        </button>
                    </div>
                </div>
                <select class="custom-select" id="firstColumn" v-model="newOption.firstColumn">
                    <optgroup label="Listen-Datensatz">
                        <option v-for="alias in listConfigColumnAliases" :value="alias">{{ alias }}</option>
                    </optgroup>
                    <optgroup label="Model-Notation-Präfix">
                        <option :value="prefix + '|'" v-for="prefix in Object.values(pluralToSingular)">{{ prefix }}|
                        </option>
                    </optgroup>
                </select>
                <select class="custom-select" id="secondColumn" v-model="newOption.secondColumn">
                    <optgroup label="Listen-Datensatz">
                        <option value=""></option>
                        <option v-for="alias in listConfigColumnAliases" :value="alias">{{ alias }}</option>
                    </optgroup>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-outline-success" type="button" @click="addMapping"
                            :disabled="!newOption.field">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
            <small class="text-muted">Werte werden konkateniert.</small>
        </template>
    </div>
</template>

<script>

import ItemsOptions from "./partials/ItemsOptions";
import ListConfigOptions from "./partials/ListConfigOptions";
import VariableOptions from "./partials/VariableOptions.vue";

export default {
    components: {
        ItemsOptions,
        ListConfigOptions,
        VariableOptions
    },
    props: {
        field: Object,
        inputOutputNames: Array,
        definition: Object,
        editable: Boolean
    },
    data() {
        return {
            pluralToSingular: {
                accesses: 'access',
                actions: 'action',
                artifacts: 'artifact',
                emails: 'email',
                documents: 'document',
                groups: 'group',
                processes: 'process',
                roles: 'role',
                relations: 'relation',
                users: 'user'
            },
            newOption: {
                field: null,
                firstColumn: null,
                secondColumn: null
            },
            listConfigColumnAliases: [],
            variable: ''
        };
    },
    computed: {
        allowCustomValues() {
            return this.field.allow_custom_values || false;
        },
        asString() {
            return this.field.as_string || false;
        },
        mapping() {
            return this.field.mapping || {};
        },
        unusedInputOutputNames() {
            return [...this.inputOutputNames].filter(ele => !Object.keys(this.mapping).includes(ele));
        },
    },
    methods: {
        onMaxItemsInput(e) {
            let value = e.target.value.trim() === '' ? '' : Math.abs(+e.target.value.trim());
            this.$emit('property-change', 'max_items', value);
        },
        onPropertyChange(property, value) {
            this.$emit('property-change', property, value);
        },
        toggleAllowCustomValues(e) {
            this.$emit('property-change', 'allow_custom_values', e.target.checked);
        },
        toggleAsString(e) {
            this.$emit('property-change', 'as_string', e.target.checked);
        },
        addMapping() {
            let mapping = {...this.mapping};
            mapping[this.newOption.field] = [];

            if (this.newOption.firstColumn) {
                mapping[this.newOption.field].push(this.newOption.firstColumn);
            }
            if (this.newOption.secondColumn) {
                mapping[this.newOption.field].push(this.newOption.secondColumn);
            }

            this.$emit('property-change', 'mapping', mapping);
            this.resetNewOption();
        },
        deleteMapping(fieldName) {
            let mapping = {...this.mapping};
            delete mapping[fieldName];

            this.$emit('property-change', 'mapping', mapping);
        },
        resetNewOption() {
            if (this.unusedInputOutputNames.length && this.listConfigColumnAliases.length) {
                this.newOption.field = this.unusedInputOutputNames[0] || '';
                this.newOption.firstColumn = this.listConfigColumnAliases[0];
                this.newOption.secondColumn = '';
            }
        },
        onUpdateListColumnAliases(aliases) {
            this.listConfigColumnAliases = aliases;

            if (!aliases.length) {
               this.$emit('property-change', 'mapping', {});
            }
        }
    }
};
</script>

