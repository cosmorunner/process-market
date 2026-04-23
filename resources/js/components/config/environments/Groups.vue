<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <ul v-for="group in blueprint.groups" class="list-group list-group-flush">
                <li class="list-group-item p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-10 p-2">
                                <span class="material-icons mx-1">group</span>
                                <span v-if="group.identity_process_type">{{ group.identity_process_type }} - </span>
                                <span v-if="group.identity_process_instance">{{
                                        getProcessName(group.identity_process_instance)
                                    }} - </span>
                                <span>{{ group.name }}</span>
                                <template v-if="group.aliases.length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">local_offer</span><span>{{
                                                group.aliases.join(', ')
                                            }}</span>
                                        </span>
                                </template>
                                <template v-if="group.tags.length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">tag</span><span>{{
                                                group.tags.join(', ')
                                            }}</span>
                                        </span>
                                </template>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right" @click="onDeleteGroup(group.id)"
                                        v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="row pb-3" v-if="ui.editable">
                <div class="col-6">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control form-control-sm" v-model="newGroup.name" required>
                        <div class="invalid-feedback d-block mt-0" v-for="error in [...ui.validationErrors.name || []]">
                            {{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Prozess-Identität</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false">
                                    <span v-if="showIdentityProcessTypeSelect">Typ</span>
                                    <span v-else>Prozess-Instanz</span>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button" @click="onChangeIdentitySource('type')">
                                        <span>Typ</span>
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="onChangeIdentitySource('instance')">
                                        <span>Prozess-Instanz</span>
                                    </button>
                                </div>
                            </div>
                            <select v-if="showIdentityProcessTypeSelect" class="form-control form-control-sm"
                                    v-model="newGroup.identity_process_type">
                                <option value="">Keine Prozess-Identität</option>
                                <option value="allisa/organisation@latest">allisa/organisation@latest</option>
                                <option v-for="graph in identityGraphs" :value="graph.full_namespace">
                                    {{ graph.full_namespace }}
                                </option>
                            </select>
                            <select v-if="showIdentityProcessInstanceSelect" class="form-control form-control-sm"
                                    v-model="newGroup.identity_process_instance">
                                <option value="">Bitte wählen...</option>
                                <option v-for="process in identityProcessInstances" :value="process.id">
                                    {{ process.name }}
                                </option>
                            </select>
                            <div class="invalid-feedback d-block mt-0"
                                 v-for="error in [...ui.validationErrors.identity_process_type || []]">
                                {{ error }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group">
                        <label>Aliases</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" v-model="newGroup.aliases" required>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false">Demo
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_1'">
                                        demo_group_1
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_2'">
                                        demo_group_2
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_3'">
                                        demo_group_3
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_4'">
                                        demo_group_4
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_5'">
                                        demo_group_5
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_6'">
                                        demo_group_6
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_7'">
                                        demo_group_7
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_8'">
                                        demo_group_8
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newGroup.aliases = 'demo_group_9'">
                                        demo_group_9
                                    </button>
                                </div>
                            </div>
                        </div>
                        <span class="text-muted"> Nur "a-z", "0-9" und Unterstrich. Optionale, kommaseparierte Identifikationen, welche die Gruppe zusätzlich identifizieren können. Aliases sind einzigartig innerhalb aller Gruppen.</span>
                        <div class="invalid-feedback d-block mt-0"
                             v-for="error in [...ui.validationErrors['aliases.0'] || []]">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Tags</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" v-model="newGroup.tags">
                        </div>
                        <div class="invalid-feedback d-block mt-0"
                             v-for="error in [...ui.validationErrors['tags'] || []]">{{ error }}
                        </div>
                        <span class="text-muted">Nur "a-z", "0-9" und Unterstrich. Optionale, kommaseparierte Tags, welche die Gruppe zusätzlich identifizieren können. Mehrere Gruppen können denselben Tag nutzen.</span>
                    </div>
                </div>
                <div class="col-1 d-flex align-items-end justify-content-end">
                    <div class="row no-gutters">
                        <button class="btn btn-sm btn-outline-primary" type="button" @click="onAddGroup">
                            <span class="material-icons">add</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ModalFooter :ui="ui" :save-label="'Speichern'" v-on="$listeners"/>
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import {v4 as uuidv4} from "uuid";
import Navigation from "./Navigation";
import ModalHeader from "../ModalHeader";
import ModalFooter from "../ModalFooter";
import utils from "../../../config-utils";
import {reduxActions} from "../../../store/develop-and-config";

export default {
    components: {
        Navigation,
        ModalHeader,
        ModalFooter,
    },
    props: {
        detailComponentName: String,
        environment: Object,
        addMode: Boolean,
        processVersionId: String
    },
    data() {
        return {
            newGroup: {
                name: '',
                aliases: '',
                tags: '',
                identity_process_type: '',
                identity_process_instance: '',
            },
            showIdentityProcessTypeSelect: true,
            showIdentityProcessInstanceSelect: false,
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs'
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
        identityGraphs() {
            // Nur Process-Versionen wo es entweder die Datenfelder "name", "description" und "group"
            // oder "name", "beschreibung" und "gruppe".
            let graphs = [...this.graphs];

            return graphs.filter(function (graph) {
                let outputNames = graph.outputs.map(ele => ele.name);

                return [
                    'name',
                    'description',
                    'group'
                ].every(ele => outputNames.includes(ele)) || [
                    'name',
                    'beschreibung',
                    'gruppe'
                ].every(ele => outputNames.includes(ele));
            }).sort((a, b) => a.full_namespace < b.full_namespace ? -1 : 1);
        },
        identityProcessInstances() {
            let processes = this.environment.blueprint.processes;
            let identityGraphsNamespaces = this.identityGraphs.map((graph) => graph.full_namespace);

            return processes.filter(function (process) {
                return identityGraphsNamespaces.includes(process.process_type);
            });
        },
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        },
        onDeleteGroup(id) {
            this.patchBlueprint('DeleteGroup', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        onAddGroup() {
            if (this.newGroup.name === '' || this.newGroup.aliases === '') {
                return;
            }

            let values = {
                ...this.newGroup,
                aliases: this.newGroup.aliases.split(',').map(ele => ele.trim()).filter(ele => ele !== ''),
                tags: this.newGroup.tags.split(',').map(ele => ele.trim()).filter(ele => ele !== '')
            };

            this.patchBlueprint('StoreGroup', values).then((response) => {
                this.resetnewGroup();
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            })
        },
        onChangeIdentitySource(source) {
            if (source === 'type') {
                this.showIdentityProcessTypeSelect = true;
                this.showIdentityProcessInstanceSelect = false;

            }
            else {
                if (source === 'instance') {
                    this.showIdentityProcessInstanceSelect = true;
                    this.showIdentityProcessTypeSelect = false;
                }
            }
            this.newGroup.identity_process_instance = '';
            this.newGroup.identity_process_type = '';
        },
        resetnewGroup() {
            this.newGroup = {
                id: uuidv4(),
                name: '',
                aliases: '',
                tags: '',
                identity_process_type: '',
                identity_process_instance: '',
            };
            this.showIdentityProcessTypeSelect = true;
            this.showIdentityProcessInstanceSelect = false;
        },
        getProcessName(processId) {
            let process = this.identityProcessInstances.find(function (intance) {
                return intance.id === processId;
            });
            return process.name;
        }
    }
};
</script>
