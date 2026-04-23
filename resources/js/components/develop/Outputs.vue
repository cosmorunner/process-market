<template>
    <div class="card mb-0 rounded-0 border-left-0 border-right-0">
        <div class="card-header d-flex align-items-center justify-content-between p-2">
            <div class="d-flex align-items-center">
                <span>Daten</span>
                <span class="badge badge-light" v-if="outputs.length">{{ outputs.length }}</span>
                <div>
                    <button class="btn btn-sm btn-outline-primary ml-1" @click="handleOpenModal(null)"
                            v-if="ui.editable">
                        <span class="material-icons">add</span>
                    </button>
                    <button class="btn btn-sm btn-outline-primary ml-1" @click="handleOpenBulkModal(null)"
                            v-if="ui.editable">
                        <span class="material-icons">add_to_photos</span>
                    </button>
                </div>
            </div>
            <div class="d-flex">
                <Docs article="process-data"/>
                <div class="input-group input-group-sm ml-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <span class="material-icons">search</span>
                        </span>
                    </div>
                    <input type="text" class="form-control" aria-label="Search" v-model="search"
                           style="max-width:100px">
                    <div class="input-group-append" v-if="search">
                        <button class="btn btn-sm btn-outline-danger" @click="search = ''">
                            <span class="material-icons text-danger">close</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-1" v-if="outputs.length">
            <table class="table mb-0">
                <thead>
                <tr class="d-flex">
                    <th scope="col" class="col-6 px-1 py-0 pb-1 border-0 font-weight-normal text-muted">
                        <small>Name</small>
                    </th>
                    <th scope="col" class="px-1 py-0 pb-1 border-0 font-weight-normal text-muted"
                        :class="{'col-5' : ui.editable, 'col-6': !ui.editable}">
                        <small>Standard-Wert</small>
                    </th>
                    <th scope="col" class="col-1 px-1 py-0 pb-1 border-0 font-weight-normal text-muted"
                        v-if="ui.editable"></th>
                </tr>
                </thead>
                <tbody class="process-data">
                <tr v-for="output in sortedOutputs" class="d-flex output-field">
                    <td class="p-1 text-break col-5 d-flex flex-column">
                        <template v-if="(editingId !== output.name)">
                            <div class="d-inline-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-link text-left"
                                        @click="handleOpenModal(output)">
                                    <span>{{ output.name }}{{
                                            (output.validation || []).includes('required') ? '*' : ''
                                        }}</span>
                                </button>
                                <span class="badge badge-light"
                                      v-if="(actionTypeId && processOutputNames.includes(output.name)) || processOutputsByActionTypes[output.name]">
                                    <span v-if="actionTypeId && processOutputNames.includes(output.name)"
                                          class="material-icons text-muted" data-toggle="tooltip" data-placement="top"
                                          title="Speicherung in Prozess-Daten">grain
                                    </span>
                                    <template v-if="processOutputsByActionTypes[output.name]">
                                        <span class="material-icons text-muted">flash_on</span>
                                        <span class="text-muted">{{ processOutputsByActionTypes[output.name] }}</span>
                                    </template>
                                </span>
                                <button type="button" class="quick-edit-btn btn btn-sm btn-light ml-2 d-none"
                                        @click="enableEditing(output.name)" v-if="editable">
                                    <span class="material-icons text-warning">edit</span>
                                </button>
                            </div>
                        </template>
                        <div v-else class="d-inline-block position-relative">
                            <input :ref="output.name" v-model="editableName" @keyup.enter="saveName(output)"
                                   class="form-control form-control-sm" @keyup.esc="cancelEditing(output.name)">
                            <div class="position-absolute z-10" style="right: -75px; top:0;">
                                <button type="button" class="btn btn-sm btn-light border" @click="saveName(output)"
                                        v-if="editableName">
                                    <span class="material-icons text-success">done</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-light ml-1 border"
                                        @click="cancelEditing(output.name)">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </div>
                        </div>
                        <div v-for="error in (ui.validationErrors.name || [])"
                             v-if="ui.errorCode && data.old_name === output.name">
                            <div class="invalid-feedback d-block mt-0 ml-2">{{ error }}</div>
                        </div>
                    </td>
                    <td class="p-1 text-break col-1 d-flex flex-column justify-content-center" data-toggle="tooltip"
                        data-placement="top" :title="getTooltip(output.type)">
                        <span class="material-icons text-muted">{{ getIcon(output.type) }}</span>
                    </td>
                    <td class="p-1 text-break" :class="{'col-5' : ui.editable, 'col-6': !ui.editable}">
                        <span v-if="output.default === null"><i class="text-muted">Kein Wert</i></span>
                        <span v-else-if="output.default === ''"><i class="text-muted">Leere Zeichenkette</i></span>
                        <span
                            v-else-if="output.type === 'array' && Array.isArray(output.default) && !output.default.length">
                            <i class="text-muted">Leeres JSON-Array</i>
                        </span>
                        <span
                            v-else-if="output.type === 'object' && typeof output.default === 'object' && !Object.keys(output.default).length">
                            <i class="text-muted">Leeres JSON-Objekt</i>
                        </span>
                        <span v-else>
                            <OptionBadgesWithText :value="output.default" v-if="typeof output.default === 'string'"/>
                            <span v-else-if="Array.isArray(output.default)">
                                <i class="text-muted">Ein JSON-Array...</i>
                            </span>
                            <span v-else>
                                <i class="text-muted">Ein JSON-Objekt...</i>
                            </span>
                        </span>
                    </td>
                    <td class="p-1 col-1" v-if="ui.editable">
                        <button class="btn btn-sm float-right btn-light"
                                @click="onDeleteOutput(actionTypeId, output.name)">
                            <span class="material-icons text-danger">delete</span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body p-1" v-if="!outputs.length">
            <span>-</span>
        </div>
    </div>
</template>

<script>

import utils from '../../develop-utils';
import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import OptionBadgesWithText from "../utils/OptionBadgesWithText";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        OptionBadgesWithText
    },
    props: {
        outputs: Array,
        actionTypeId: String,
        updateMethod: String,
        updateBulkMethod: String,
        storeMethod: String,
        storeBulkMethod: String,
        editable: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            data: {
                action_type_id: null,
                default: '',
                description: '',
                name: '',
                old_name: '',
                type: '',
                type_options: null,
                validation: null
            },
            editableName: '',
            editingId: null,
            search: ''
        };
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        handleOpenModal(output) {
            this.openModal({
                componentName: 'OutputModal',
                data: {
                    actionTypeId: this.actionTypeId,
                    output: output,
                    method: output ? this.updateMethod : this.storeMethod
                }
            });
        },
        setEditingId(name) {
            if (name && this.editingId) {
                // $refs returns an array of vue-components
                const oldOutput = this.sortedOutputs.find(output => output.name === this.editingId);
                this.saveName(oldOutput);
            }

            this.editingId = name;
        },
        updateOutputName(output) {
            let data = {
                ...output,
                action_type_id: this.actionTypeId,
                old_name: output.name,
                name: this.editableName
            };

            this.patchDefinition(this.updateMethod, data, false)
                .then(this.closeModal)
                .catch(() => {
                });
        },
        enableEditing(name) {
            this.setEditingId(name);
            this.editableName = name;

            // So the focus is set after the redering of the input field is done
            this.$nextTick(() => {
                this.$refs[name][0].focus();
            });
        },
        cancelEditing(name) {
            this.editableName = name;
            this.setEditingId(null);
        },
        saveName(output) {
            if (this.editingId !== null) {
                if (output && this.editableName.trim() !== output.name && this.editableName.trim()) {
                    this.updateOutputName(output);
                }
                this.setEditingId(null);
            }
        },
        handleOpenBulkModal() {
            let formatString, examplesArray;

            // Opening of bulk modal for a action data fields
            if (this.actionTypeId) {
                formatString = '<Typ(|~|=)><Name><Pflichtfeld(|!)>;<?Standard-Wert>;';
                examplesArray = [
                    {
                        syntax: 'text_1',
                        description: 'Datenfeld "text_1" mit einer leeren Zeichenkette als Standard-Wert.'
                    },
                    {
                        syntax: 'text_1!',
                        description: 'Pflichtfeld "text_1" mit einer leeren Zeichenkette als Standard-Wert.'
                    },
                    {
                        syntax: 'text_2;null',
                        description: 'Datenfeld "text_2" ohne Standard-Wert.'
                    },
                    {
                        syntax: 'text_3;Hello World',
                        description: 'Datenfeld "text_3" mit "Hello World" als Standard-Wert.'
                    },
                    {
                        syntax: 'text_1;;1',
                        description: 'Datenfeld "text_1" mit gleichnamigen Prozess-Datenfeld.'
                    },
                    {
                        syntax: 'text_1;;;1',
                        description: 'Datenfeld "text_1" mit gleichnamigen Aktions-Vorlade-Datenfeld.'
                    },
                    {
                        syntax: 'text_1;;;1;1',
                        description: 'Datenfeld "text_1" mit gleichnamigen Aktions-Vorlade-Datenfeld und Prozess-Datenfeld als Wert.'
                    },
                    {
                        syntax: 'text_1;;;;;1',
                        description: 'Datenfeld "text_1" mit gleichnamigen Formular-Feld'
                    },
                    {
                        syntax: '=liste_1',
                        description: 'JSON-Array Datenfeld "liste_1" mit einer leeren JSON-Array als Standard-Wert.'
                    },
                    {
                        syntax: '~objekt_1',
                        description: 'JSON-Objekt Datenfeld "objekt_1" mit einem leeren JSON-Objekt als Standard-Wert.'
                    },
                ];
            }
            // Opening of bulk modal for a process data field
            else {
                formatString = '<Typ(|~|=)><Name>;<?Standard-Wert>';
                examplesArray = [
                    {
                        syntax: 'text_1',
                        description: 'Einfaches Datenfeld "text_1" mit einer leeren Zeichenkette als Standard-Wert.'
                    },
                    {
                        syntax: 'text_2;null',
                        description: 'Einfaches Datenfeld "text_2" ohne Standard-Wert.'
                    },
                    {
                        syntax: 'text_3;Hello World',
                        description: 'Einfaches Datenfeld "text_3" mit "Hello World" als Standard-Wert.'
                    },
                    {
                        syntax: '=liste_1',
                        description: 'JSON-Array Datenfeld "liste_1" mit einer leeren JSON-Array als Standard-Wert.'
                    },
                    {
                        syntax: '~objekt_1',
                        description: 'JSON-Objekt Datenfeld "objekt_1" mit einem leeren JSON-Objekt als Standard-Wert.'
                    },
                ];
            }

            this.openModal({
                componentName: 'BulkModalText',
                data: {
                    method: this.storeBulkMethod,
                    title: this.actionTypeId ? 'Datenfeld' : 'Prozess-Datenfelder',
                    article: this.actionTypeId ? 'rules-actions' : 'process-data',
                    section: this.actionTypeId ? 'bulk-create-action-data' : 'bulk-create-data',
                    methodData: {
                        action_type_id: this.actionTypeId ? this.actionTypeId : null,
                    },
                    format: formatString,
                    examples: examplesArray,
                    requiresDeleteHtml: false,
                }
            });
        },
        getIcon(type) {
            return {
                'object': 'data_object',
                'array': 'data_array',
            }[type] ?? 'text_fields'; // Default
        },
        getTooltip(type) {
            return {
                'object': 'Objekt',
                'array': 'Array',
            }[type] ?? 'Einfach';
        }
    },
    computed: {
        ...mapGetters([
            'definition',
            'status_types',
            'action_types',
            'ui'
        ]),
        sortedOutputs() {
            return [...this.outputs].sort((a, b) => a.name.localeCompare(b.name))
                .filter((a) => !this.search ? true : a.name.toLowerCase().includes(this.search.toLowerCase()));
        },
        processOutputNames() {
            return this.definition.outputs.map(ele => ele.name);
        },
        processOutputsByActionTypes() {
            let obj = {};

            if (this.actionTypeId) {
                return obj;
            }

            for (let i = 0; i < this.processOutputNames.length; i++) {
                let name = this.processOutputNames[i];
                obj[name] = this.action_types.filter(ele => ele.outputs.map(output => output.name).includes(name)).length;
            }

            return obj;
        }
    },
    mounted() {
        $('[data-toggle="tooltip"]').tooltip();
    }
};
</script>

<style scoped>
.output-field:hover .quick-edit-btn {
    display: inline-block !important;
}
</style>