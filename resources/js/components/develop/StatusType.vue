<template>
    <div class="card mb-0 rounded-0 border-left-0 border-right-0 border-bottom-0 status-type">
        <div
            class="card-header status-type-header d-flex align-items-center border-bottom-0 justify-content-between bg-white px-2 py-1"
            v-if="!hideHeader">
            <div>
                <template v-if="($parent.editingId !== statusType.id)">
                    <div class="d-inline-flex align-items-center">
                        <button type="button" class="btn btn-link p-0 text-left"
                                @click="showStatusTypeDetail(statusType.id)">
                            <span>{{ statusType.name }}</span>
                        </button>
                        <button type="button" class="quick-edit-btn btn btn-sm btn-light ml-2 d-none" v-if="editMode"
                                @click="enableStatusTypeEditing(statusType)">
                            <span class="material-icons text-warning">edit</span>
                        </button>
                    </div>
                </template>
                <div v-else class="d-flex">
                    <input :ref="statusType.reference" v-model="editableName" @keyup.enter="saveStatusTypeName"
                           @keyup.esc="cancelStatusTypeEditing(statusType.name)" class="form-control form-control-sm"/>
                    <button type="button" class="btn btn-sm btn-light ml-2 border" @click="saveStatusTypeName"
                            v-if="editableName">
                        <span class="material-icons text-success">done</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-light ml-1 border"
                            @click="cancelStatusTypeEditing(statusType.name)">
                        <span class="material-icons text-danger">close</span>
                    </button>
                </div>
            </div>
            <div v-if="isSmart">
                <span class="badge badge-primary">Smart-Status</span>
            </div>
            <div>
                <div v-if="editMode && ui.editable" class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-light" type="button" id="actionDropDownButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">more_vert</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="actionDropDownButton">
                        <button v-on:click="onEditStatusType" class="dropdown-item" type="button">
                            Bearbeiten
                        </button>
                        <div class="dropdown-divider"></div>
                        <button v-on:click="onDeleteStatusType(statusType.id)" class="dropdown-item text-danger"
                                type="button">Löschen
                        </button>
                    </div>
                </div>
                <a :href="configUrl('StatusTypes')" class="btn btn-sm btn-light" v-if="!simulation.running">
                    <span class="material-icons">list_alt</span>
                </a>
            </div>
        </div>
        <div class="card-body p-0 pr-2" v-if="showStates">
            <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0 border-top-0" v-if="editMode && !onlyInitial">
                <div
                    class="card-header bg-white d-flex justify-content-between align-items-center px-2 py-1 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary" @click="onAddState">
                            <span class="material-icons">add</span> Zustand Hinzufügen
                        </button>
                        <button class="btn btn-sm btn-outline-primary ml-2" @click="handleOpenBulkModal(null)"
                                v-if="editMode">
                            <span class="material-icons">add_to_photos</span>
                        </button>
                    </div>
                    <Docs article="rules-status" section="create-state"/>
                </div>
            </div>
            <div class="card rounded-0 border-0" v-if="!onlyForValue && !onlyInitial && showStates">
                <div class="card-body px-2 py-1" v-if="!isSmart">
                    <span class="text-muted d-block">Referenz-Name: {{ statusType.reference }}</span>
                    <span class="text-muted d-block">Initial-Wert: <span class="badge badge-pill badge-light">{{ statusType.default.endsWith('.000') ? parseInt(statusType.default) : statusType.default }}</span></span>
                </div>
            </div>
            <table class="table mb-0" v-if="states.length">
                <tbody>
                <tr v-for="(state, index) in states" class="state-header">
                    <td class="col-12 pl-2 py-1 pr-0 " :class="index === 0 ? 'border-0' : ''">
                        <div class="d-flex align-items-center justify-content-between">
                            <template v-if="(stateEditingId !== state.id) && editMode">
                                <div class="d-inline-flex align-items-center">
                                    <span>{{ state.description }}</span>
                                    <div class="d-inline-block">
                                        <small class="badge badge-pill badge-light ml-2" v-if="state.min === state.max">{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }}</small>
                                        <small class="badge badge-pill badge-light ml-2" v-else>{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }} - {{ state.max.endsWith('.000') ? parseInt(state.max) : state.max }}</small>
                                    </div>
                                    <button type="button" class="quick-edit-btn btn btn-sm btn-light ml-2 d-none"
                                            @click="enableStateEditing(state)" v-if="editMode">
                                        <span class="material-icons text-warning">edit</span>
                                    </button>
                                </div>
                            </template>
                            <div v-else-if="stateEditingId === state.id" class="d-flex">
                                <input :ref="state.id" v-model="editableName" @keyup.enter="saveStateName(state)"
                                       @keyup.esc="cancelStateEditing(state.description)"/>
                                <button type="button" class="btn btn-sm btn-light ml-2" @click="saveStateName(state)"
                                        v-if="editableName">
                                    <span class="material-icons text-success">done</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-light ml-1"
                                        @click="cancelStateEditing(state)">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </div>
                            <div v-if="!editMode">
                                <span v-if="stateDescription">{{ stateDescription }}</span>
                                <span v-else>{{ state.description }}</span>
                                <div v-if="onlyForValue" class="d-inline-block">
                                    <small class="badge badge-pill badge-light ml-2">{{ onlyForValue.endsWith('.000') ? parseInt(onlyForValue) : onlyForValue }}</small>
                                </div>
                                <div v-if="!onlyForValue" class="d-inline-block">
                                    <small class="badge badge-pill badge-light ml-2" v-if="state.min === state.max">{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }}</small>
                                    <small class="badge badge-pill badge-light ml-2" v-else>{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }} - {{ state.max.endsWith('.000') ? parseInt(state.max) : state.max }}</small>
                                </div>
                            </div>
                            <div v-if="editMode">
                                <button class="btn btn-sm btn-light" @click="onEditState(state)">
                                    <span class="material-icons text-warning">edit</span>
                                </button>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-light" type="button" id="actionDropDownButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="material-icons">more_vert</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="actionDropDownButton">
                                        <button class="dropdown-item text-danger"
                                                @click="onDeleteState(state.status_type_id, state.id)">
                                            <span class="text-danger">Löschen</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="card mb-0 rounded-0 border-0" v-else>
                <div class="card-body px-2 py-1">
                    <span>Erstellen Sie einen neuen Zustand mit einem Rechtsklick in den Status auf der Prozess-Fläche.</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import Docs from "../utils/Docs";

export default {
    components: {Docs},
    props: {
        statusType: Object,
        hideFooter: {
            type: Boolean,
            default: false
        },
        hideHeader: {
            type: Boolean,
            default: false
        },
        onlyInitial: {
            type: Boolean,
            default: false
        },
        onlyForValue: {
            default: false
        },
        stateDescription: {
            default: ''
        },
        editable: {
            type: Boolean,
            default: false
        },
        showStates: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            stateEditingId: '',
            editableName: ''
        };
    },
    computed: {
        ...mapGetters([
            'action_types',
            'status_types',
            'states',
            'simulation',
            'definition',
            'ui',
        ]),
        states: function () {
            if (this.onlyInitial) {
                return this.statusType.states.filter(ele => +this.statusType.default >= +ele.min && +this.statusType.default <= +ele.max);
            }

            if (typeof this.onlyForValue === 'string') {
                let states = this.statusType.states.filter(ele => +this.onlyForValue >= +ele.min && +this.onlyForValue <= +ele.max);
                return states.length === 1 ? states : [
                    {
                        color: '#013370',
                        description: 'Nicht definiert: ' + this.onlyForValue,
                        image: 'help_outline',
                        min: '',
                        max: '',
                        status_type_id: this.statusType.id,
                        visible: 1
                    }
                ];
            }

            let states = [...this.statusType.states];

            return states.sort((a, b) => +a.min.localeCompare(+b.min));
        },
        editMode() {
            return !this.simulation.running && this.editable;
        },
        isSmart() {
            return Object.keys(this.statusType.smart || {}).length;
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onEditStatusType() {
            this.openModal({
                componentName: 'StatusTypeModal',
                data: {
                    position: null,
                    statusTypeId: this.statusType.id
                }
            });
        },
        onEditState(state) {
            this.openModal({
                componentName: 'StateModal',
                data: {
                    position: null,
                    stateId: state.id,
                    statusTypeId: state.status_type_id
                }
            });
        },
        onAddState() {
            this.openModal({
                componentName: 'StateModal',
                data: {
                    position: null,
                    stateId: null,
                    statusTypeId: this.statusType.id
                }
            });
        },
        enableStatusTypeEditing(statusType) {
            this.editableName = statusType.name;
            this.$emit('setEditId', statusType.id);

            // So the focus is set after the redering of the input field is done
            this.$nextTick(() => {
                this.$refs[statusType.reference].focus();
            });
        },
        enableStateEditing(state) {
            this.editableName = state.description;
            this.setStateEditingId(state);

            // So the focus is set after the redering of the input field is done
            this.$nextTick(() => {
                this.$refs[state.id][0].focus();
            });
        },
        cancelStatusTypeEditing(name) {
            this.editableName = name;
            this.$emit('setEditId', null);
        },
        cancelStateEditing(state) {
            this.editableName = state.name;
            this.stateEditingId = null;
        },
        saveStatusTypeName() {
            if (this.$parent.editingId !== null) {
                if (this.statusType && this.editableName.trim() !== this.statusType.name && this.editableName.trim()) {
                    this.patchDefinition('UpdateStatusType', {
                        ...this.statusType,
                        name: this.editableName.trim()
                    }).catch(() => {
                    });
                }
                this.$emit('setEditId', null);
            }
        },
        saveStateName(state) {
            if (this.stateEditingId !== null) {
                if (state && this.editableName.trim() !== state.description && this.editableName.trim()) {
                    this.patchDefinition('UpdateState', {
                        ...state,
                        description: this.editableName.trim(),
                    }).catch(() => {
                    });
                }
                this.stateEditingId = null;
            }
        },
        setStateEditingId(state) {
            if (state && this.stateEditingId) {
                // $refs returns an array of vue-components
                this.saveStateName(state);
            }

            this.stateEditingId = state ? state.id : null;
        },
        handleOpenBulkModal() {
            this.openModal({
                componentName: 'BulkModalText',
                data: {
                    position: null,
                    title: 'Status-Zustände',
                    article: 'rules-status',
                    section: 'bulk-create-state',
                    method: 'StoreStateBulk',
                    methodData: {
                        status_type_id: this.statusType.id,
                        status_type_default: this.statusType.default
                    },
                    format: '<Beschreibung>;<?Min-Wert>;<?Max-Wert>',
                    examples: [
                        {
                            syntax: 'Mein Zustand',
                            description: 'Zustand "Mein Zustand" mit einem automatischen Wert.'
                        },
                        {
                            syntax: 'Mein Zustand;10',
                            description: 'Zustand "Mein Zustand" mit dem Wert 10.'
                        },
                        {
                            syntax: 'Mein Zustand;10-15',
                            description: 'Zustand "Mein Zustand" mit einem Wertebereich 10-15.'
                        }
                    ]
                }
            });
        }
    }
};
</script>

<style scoped>
.status-type-header:hover .quick-edit-btn, .state-header:hover .quick-edit-btn {
    display: inline-block !important;
}
</style>
