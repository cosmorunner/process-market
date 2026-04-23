<template>
    <div>
        <div class="modal-header py-2">
            <h5 class="modal-title" id="exampleModalLabel">
                <button class="btn btn-sm btn-primary mr-2" @click="$emit('navigation-change', 'Processes')">
                    <span class="material-icons">keyboard_backspace</span>
                </button>
                <span>{{ addMode ? 'Prozess hinzufügen' : 'Prozess bearbeiten' }}</span>
            </h5>
            <button type="button" class="close" aria-label="Close" @click="$emit('cancel')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body py-2">
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="process_type">Prozesstyp</label>
                <select class="form-control" id="process_type" name="process_type" v-model="process.process_type" :disabled="!ui.editable">
                    <option v-if="!graphs.find(ele => ele.full_namespace === process.process_type)" :value="process.process_type">{{process.process_type}}</option>
                    <option v-for="graph in sortedGraphs" :value="graph.full_namespace">
                        {{ graph.full_namespace }}
                    </option>
                </select>
                <small class="text-muted">Es werden nur fertiggestellte Versionen angezeigt.</small>
                <div v-for="error in (ui.validationErrors.process_type || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required v-model="process.name"
                       maxlength="40" :readonly="!ui.editable"/>
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <template v-if="ui.editable || Object.keys(process.initial_situation).length">
                <span class="d-block">Situation</span>
                <AddSituation :situation-prop="process.initial_situation"
                              :status-types="selectedStatusTypes"
                              @add-situation-option="onAddSituationOption"
                              @delete-situation-option="onDeleteSituationOption"
                              @clear-situation-option="clearSituationOption"
                              :editable="ui.editable"
                />
            </template>
            <template v-if="ui.editable || Object.keys(process.initial_data).length">
                <span class="d-block">Prozess-Daten</span>
                <AddProcessData :process-data-prop="process.initial_data"
                                :outputs="selectedOutputs"
                                @add-process-data-option="onAddProcessDataOption"
                                @delete-process-data-option="onDeleteProcessDataOption"
                                @clear-process-data-option="clearProcessDataOption"
                                :editable="ui.editable"
                />
            </template>
            <template >
                <span class="d-block">Zugriffe</span>
                <AddAccess :accesses-prop="process.accesses"
                           :roles="selectedRoles"
                           :groups="environment.blueprint.groups || []"
                           :users="environment.blueprint.users || []"
                           @update-access-option="onUpdateAccessOption"
                           @delete-access-option="onDeleteAccessOption"
                           @clear-access-option="clearAccessOption"
                           :editable="ui.editable"

                />
            </template>
        </div>
        <ModalFooter @save="onSave" @cancel="onCancel" :ui="ui" :save-label="addMode ? 'Hinzufügen' : 'Übernehmen'"/>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {v4 as uuidv4} from 'uuid';
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import AddSituation from "./AddSituation";
import AddProcessData from "./AddProcessData";
import AddAccess from "./AddAccess";

export default {
    components: {
        ModalHeader,
        ModalFooter,
        AddSituation,
        AddProcessData,
        AddAccess
    },
    props: {
        environment: Object,
        processVersionId: String,
        data: {
            required: true,
            default: null
        }
    },
    data() {
        let data = this.data;
        let addMode = this.data === null;

        if (!data) {
            data = {
                id: uuidv4(),
                process_type: '',
                name: '',
                initial_data: {},
                initial_situation: {},
                accesses: {}
            };
        }

        return {
            process: {...data},
            addMode: addMode
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs'
        ]),
        sortedGraphs(){
            return [...this.graphs].sort((a, b) => a.full_namespace > b.full_namespace ? 1 : -1)
        },
        selectedStatusTypes() {
            let processType = this.graphs.find(ele => ele.full_namespace === this.process.process_type);
            return processType ? processType.status_types : [];
        },
        selectedOutputs() {
            let processType = this.graphs.find(ele => ele.full_namespace === this.process.process_type);
            return processType ? processType.outputs : [];
        },
        selectedRoles() {
            let processType = this.graphs.find(ele => ele.full_namespace === this.process.process_type);
            return processType ? processType.roles : [];
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreProcess' : 'UpdateProcess';

            this.patchBlueprint(method, this.process).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
                this.$emit('navigation-change', 'Processes');
            });
        },
        onCancel() {
            this.clearError();
            this.$emit('navigation-change', 'Processes');
        },
        onAddSituationOption(statusTypeReference, value) {
            if (Object.keys(this.process.initial_situation).includes(statusTypeReference)) {
                return;
            }

            this.process.initial_situation = {
                ...this.process.initial_situation,
                [statusTypeReference]: value
            };
        },
        onDeleteSituationOption(statusTypeReference) {
            let situation = {...this.process.initial_situation};
            delete situation[statusTypeReference];
            this.process.initial_situation = situation;
        },
        clearSituationOption() {
            this.process.initial_situation = {};
        },
        onAddProcessDataOption(object) {
            if (Object.keys(this.process.initial_data).includes(object.name)) {
                return;
            }

            this.process.initial_data = {
                ...this.process.initial_data,
                [object.name]: object.value
            };
        },
        onDeleteProcessDataOption(name) {
            let processData = {...this.process.initial_data};
            delete processData[name];
            this.process.initial_data = processData;
        },
        clearProcessDataOption() {
            this.process.initial_data = {};
        },
        onUpdateAccessOption(groupId, roleId) {
            this.process.accesses = {
                ...this.process.accesses,
                [groupId]: roleId
            };
        },
        onDeleteAccessOption(name) {
            let accesses = {...this.process.accesses};
            delete accesses[name];
            this.process.accesses = accesses;
        },
        clearAccessOption() {
            this.process.accesses = {};
        }
    },
    watch: {
        process: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }
};
</script>
