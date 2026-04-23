<template>
    <div>
        <ListConfigOptions :field="field" @property-change="onPropertyChange" :editable="editable"
                           :input-output-names="inputOutputNames" :enable-label-field-alias="false"
                           :enable-value-field-alias="false"
                           @updated-list-config-column-aliases="listConfigColumnAliases = $event"/>
        <div class="mb-3" v-if="field.list_config.id">
            <span class="d-block"></span>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="autoMapping"
                       @click="toggleAutoMappingSingleListEntry" :checked="field.auto_mapping_on_single_list_entry"
                       :disabled="!editable">
                <label class="custom-control-label" for="autoMapping">Auto-Mapping bei einzelnen Listenergebnis</label>
                <small class="text-muted d-block">Zeilenwerte automatisch auf Felder mappen, wenn die Dialogliste nur
                    einen Eintrag hat.</small>
            </div>
        </div>
        <div class="mb-3" v-if="field.list_config.id">
            <span class="d-block"></span>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="autoMappingOnLoad"
                       @click="toggleAutoMappingOnLoad" :checked="field.auto_mapping_on_load" :disabled="!editable">
                <label class="custom-control-label" for="autoMappingOnLoad">Auto-Mapping beim Öffnen der Aktion</label>
                <small class="text-muted d-block">Auto-Mapping beim Öffnen der Aktion ausführen (Überschreibt ggf.
                    Vorladewert).</small>
            </div>
        </div>
        <div class="form-group mb-2">
            <label for="button_label" class="mb-0">Button-Label</label>
            <input type="text" class="form-control form-control-sm" :readonly="!editable" id="button_label"
                   v-model="field.button_label" aria-describedby="button_label"/>
        </div>
        <label class="mb-0">Wert</label>
        <small class="text-muted d-block">Eintrag aus Listenzeile als Feldwert übernehmen. Hinweis:
            Listenkonfiguration benötigt einen "Mapping"-Button.</small>
        <div class="input-group input-group-sm my-2" v-if="editable">
            <select class="custom-select" id="firstValueColumn" v-model="firstValueMapping"
                    @change="updateValueMapping(0, $event.target.value)">
                <optgroup label="Listen-Datensatz">
                    <option value="">Kein Mapping</option>
                    <option v-for="alias in listConfigColumnAliases" :value="alias">{{ alias }}</option>
                </optgroup>
                <optgroup label="Model-Notation-Präfix">
                    <option :value="prefix + '|'" v-for="prefix in Object.values(pluralToSingular)">{{ prefix }}|
                    </option>
                </optgroup>
            </select>
            <select class="custom-select" id="secondValueColumn" v-model="secondValueMapping"
                    @change="updateValueMapping(1, $event.target.value)" :disabled="!firstValueMapping">
                <optgroup label="Listen-Datensatz">
                    <option value=""></option>
                    <option v-for="alias in listConfigColumnAliases" :value="alias">{{ alias }}</option>
                </optgroup>
            </select>
        </div>

        <label class="mb-0">Weiteres Mapping</label>
        <small class="text-muted d-block">Weitere Werte der gewählten Listenzeile auf Felder übernehmen.</small>
        <table class="table table-sm table-borderless mt-2 mb-0" v-if="Object.keys(mapping).length">
            <thead>
            <tr>
                <th class="font-weight-normal w-30 border-bottom">Feld</th>
                <th class="font-weight-normal w-30 border-bottom">Wert 1</th>
                <th class="font-weight-normal w-30 border-bottom">Wert 2</th>
                <th class="font-weight-normal w-10 border-bottom"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(columns, fieldName) in mapping">
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
                    <button class="btn btn-sm btn-light float-right" @click="deleteMapping(fieldName)" v-if="editable">
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
                <button class="btn btn-outline-success" type="button" @click="addMapping" :disabled="!newOption.field">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
        <small class="text-muted">Werte werden konkateniert.</small>
    </div>
</template>

<script>

import ListConfigOptions from "./partials/ListConfigOptions";
import {mapGetters} from "vuex";

export default {
    components: {
        ListConfigOptions,
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
            firstValueMapping: (this.field.value_mapping || [])[0] || '',
            secondValueMapping: (this.field.value_mapping || [])[1] || '',
            listConfigColumnAliases: [],
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        mapping() {
            return this.field.mapping || {};
        },
        unusedInputOutputNames() {
            return [...this.inputOutputNames].filter(ele => !Object.keys(this.mapping).includes(ele));
        },
    },
    methods: {
        onPropertyChange(property, value) {
            this.$emit('property-change', property, value);
        },
        updateValueMapping(index, column) {
            let valueMapping = [
                this.firstValueMapping,
                this.secondValueMapping
            ];
            valueMapping[index] = column;

            this.$emit('property-change', 'value_mapping', valueMapping);
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
        toggleAutoMappingSingleListEntry() {
            this.$emit('property-change', 'auto_mapping_on_single_list_entry', !this.field.auto_mapping_on_single_list_entry);
        },
        toggleAutoMappingOnLoad() {
            this.$emit('property-change', 'auto_mapping_on_load', !this.field.auto_mapping_on_load);
        },
    },
    mounted() {
        if (this.definition.list_configs.length && !this.field.list_config.id) {
            let listConfig = this.definition.list_configs[0];

            this.$emit('property-change', 'list_config', {
                id: listConfig.id
            });
        }

        this.resetNewOption();
    }
};
</script>

