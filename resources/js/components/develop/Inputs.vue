<template>
    <div class="card mb-0 rounded-0 border-left-0 border-right-0">
        <div class="card-header d-flex align-items-center justify-content-between p-2" v-if="editMode">
            <div class="d-flex align-items-center">
                <span>Vorlade-Daten</span>
                <span class="badge badge-light" v-if="inputs.length">{{ inputs.length }}</span>
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
        <div class="card-body p-1" v-if="inputs.length">
            <table class="table mb-0">
                <thead>
                <tr class="d-flex">
                    <th scope="col" class="col-6 px-1 py-0 pb-1 border-0 font-weight-normal text-muted">
                        <small>Name</small>
                    </th>
                    <th scope="col" class="px-1 py-0 pb-1 border-0 font-weight-normal text-muted"
                        :class="{'col-5' : ui.editable, 'col-6': !ui.editable}">
                        <small>Wert</small>
                    </th>
                    <th v-if="editMode" scope="col"
                        class="col-1 px-1 py-0 pb-1 border-0 font-weight-normal text-muted"></th>
                </tr>
                </thead>
                <tbody class="preprocess-data">
                <tr v-for="input in sortedInputs" class="d-flex output-field">
                    <td class="p-1 text-break col-5">
                        <template v-if="(editingId !== input.name)">
                            <div class="d-inline-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-link text-left"
                                        @click="handleOpenModal(input)">
                                    <span>{{ input.name }}</span>
                                </button>
                                <button type="button" class="quick-edit-btn btn btn-sm btn-light ml-2 d-none"
                                        @click="enableEditing(input.name)" v-if="editable">
                                    <span class="material-icons text-warning">edit</span>
                                </button>
                            </div>
                        </template>
                        <div v-else class="d-inline-block position-relative">
                            <input :ref="input.name" v-model="editableName" @keyup.enter="saveName(input)"
                                   class="form-control form-control-sm" @keyup.esc="cancelEditing(input.name)">
                            <div class="position-absolute z-10" style="right: -75px; top:0;">
                                <button type="button" class="btn btn-sm btn-light border" @click="saveName(input)"
                                        v-if="editableName">
                                    <span class="material-icons text-success">done</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-light ml-1 border"
                                        @click="cancelEditing(input.name)">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="p-1 text-break col-1 d-flex flex-column justify-content-center" data-toggle="tooltip"
                        data-placement="top" :title="getTooltip(input.type)">
                        <span class="material-icons text-muted">{{ getIcon(input.type) }}</span>
                    </td>
                    <td class="p-1 text-break" :class="{'col-5' : ui.editable, 'col-6': !ui.editable}">
                        <span v-if="input.value === null"><i class="text-muted">Kein Wert</i></span>
                        <span v-else-if="input.value === ''"><i class="text-muted">Leere Zeichenkette</i></span>
                        <span v-else-if="input.type === 'array' && Array.isArray(input.value) && !input.value.length">
                            <i class="text-muted">Leere Liste</i>
                        </span>
                        <span
                            v-else-if="input.type === 'object' && typeof input.value === 'object' && !Object.keys(input.value).length">
                            <i class="text-muted">Leeres Objekt</i>
                        </span>
                        <span v-else-if="input.type === 'list_config'">
                            <OptionBadges :value="input.type_options.list_config"/>
                            <span class="d-block text-muted">{{
                                    input.type_options.column ? input.type_options.column : 'Komplette Liste'
                                }}</span>
                        </span>
                        <span v-else>
                            <OptionBadgesWithText :value="input.value" v-if="typeof input.value === 'string'"/>
                            <span v-else-if="Array.isArray(input.value)">
                                <i class="text-muted">Ein JSON-Array...</i>
                            </span>
                            <span v-else>
                                <i class="text-muted">Ein JSON-Objekt...</i>
                            </span>
                        </span>
                    </td>
                    <td class="p-1 col-1" v-if="editMode">
                        <button class="btn btn-sm float-right btn-light" @click="onDeleteInput(input.name)">
                            <span class="material-icons text-danger">delete</span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body p-1" v-if="!inputs.length">
            <span>-</span>
        </div>
    </div>
</template>

<script>
import utils from "../../develop-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";
import OptionBadges from "../utils/OptionBadges";
import OptionBadgesWithText from "../utils/OptionBadgesWithText";

export default {
    components: {
        OptionBadgesWithText,
        OptionBadges
    },
    props: {
        inputs: Array,
        actionTypeId: String,
        editMode: {
            default: true,
            type: Boolean
        },
        editable: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            data: {
                action_type_id: '',
                name: '',
                old_name: '',
                type: '',
                type_options: {},
                value: ''
            },
            editingId: '',
            editableName: '',
            'search': ''
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        sortedInputs() {
            return [...this.inputs].sort((a, b) => a.name.localeCompare(b.name))
                .filter((a) => !this.search ? true : a.name.toLowerCase().includes(this.search.toLowerCase()));
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onDeleteInput(inputName) {
            this.patchDefinition('DeleteActionTypeInput', {
                action_type_id: this.actionTypeId,
                name: inputName
            }, false).catch(() => {
            });
        },
        handleOpenModal(input) {
            this.openModal({
                componentName: 'InputModal',
                data: {
                    actionTypeId: this.actionTypeId,
                    input: input,
                    method: input ? 'UpdateActionTypeInput' : 'StoreActionTypeInput'
                }
            });
        },
        updateName(input) {
            let data = {
                ...input,
                action_type_id: this.actionTypeId,
                old_name: input.name,
                name: this.editableName
            };

            this.patchDefinition('UpdateActionTypeInput', data, false).catch(() => {
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
        cancelEditing(input) {
            this.editableName = input.name;
            this.editingId = null;
        },
        saveName(input) {
            if (this.editingId !== null) {
                if (input && this.editableName.trim() !== input.name && this.editableName.trim()) {
                    this.updateName(input);
                }
                this.editingId = null;
            }
        },
        setEditingId(name) {
            if (name && this.editingId) {
                // $refs returns an array of vue-components
                const oldInput = this.sortedInputs.find(input => input.name === this.editingId);
                this.saveName(oldInput);
            }

            this.editingId = name;
        },
        handleOpenBulkModal(input) {
            this.openModal({
                componentName: 'BulkModalText',
                data: {
                    title: "Vorlade-Datenfelder",
                    method: input ? 'StoreActionTypeInputBulk' : 'StoreActionTypeInputBulk',
                    article: 'rules-actions',
                    section: 'bulk-create-preload-data',
                    methodData: {
                        action_type_id: this.actionTypeId
                    },
                    format: '<Typ(|~|=)><Name>;<?Wert>',
                    examples: [
                        {
                            syntax: 'text_1',
                            description: 'Datenfeld "text_1" vom Typ "Zeichenkette" mit einer leeren Zeichenkette als Wert.'
                        },
                        {
                            syntax: 'text_2;Hello World',
                            description: 'Datenfeld "text_2" vom Typ "Zeichenkette" mit "Hello World" als Wert.'
                        },
                        {
                            syntax: 'text_3;#',
                            description: 'Datenfeld "text_3" vom Typ "Automatisch" mit dem gleichnamigen Prozess-Datenfeld als Wert.'
                        },
                        {
                            syntax: '=liste_1',
                            description: 'JSON-Array Datenfeld "liste_1" mit einer leeren Liste als Wert.'
                        },
                        {
                            syntax: '~objekt_1',
                            description: 'JSON-Objekt Datenfeld "liste_1" mit einer leeren Liste als Wert.'
                        },
                    ],
                    requiresDeleteHtml: false
                }
            });
        },
        getIcon(type) {
            return {
                'object': 'data_object',
                'array': 'data_array',
                'auto': 'hdr_auto',
                'list_config': 'view_list'
            }[type] ?? 'short_text'; // Default
        },
        getTooltip(type) {
            return {
                'object': 'Objekt',
                'array': 'Array',
                'auto': 'Automatisch',
                'list_config': 'Listen-Inhalt'
            }[type] ?? 'Zeichenkette';
        }
    }
};
</script>

<style scoped>
.output-field:hover .quick-edit-btn {
    display: inline-block !important;
}
</style>