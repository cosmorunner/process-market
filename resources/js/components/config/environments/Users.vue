<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-1 p-2">
                                <span class="material-icons">person</span>
                            </div>
                            <div class="col-9 p-2">
                                <span>allisa/person@latest</span>
                                <span> - </span>
                                <span>Demo Benutzer</span>
                                <span> - </span>
                                <span class="text-muted">demo@example.com</span>
                                <small class="text-muted d-block"><span class="material-icons mr-1">group</span><span>Standard - default</span>
                                    <span class="d-inline-block ml-2">
                                        <span class="material-icons mr-1">local_offer</span><span>demo_user</span>
                                    </span>
                                </small>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right" disabled>
                                    <span class="material-icons">lock</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul v-for="user in blueprint.users" class="list-group list-group-flush">
                <li class="list-group-item p-0" :class="{'bg-light' : user === editedUser}">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-1 p-2">
                                <span class="material-icons">person</span>
                            </div>
                            <div class="col-9 p-2">
                                <span v-if="user.identity_process_type">{{ user.identity_process_type }}</span>
                                <span v-if="user.identity_process_instance">{{
                                        getProcessName(user.identity_process_instance)
                                    }}</span>
                                <span> - </span>
                                <span>{{ user.first_name + ' ' + user.last_name }}</span>
                                <span> - </span>
                                <span class="text-muted">{{ user.email }}</span>
                                <small class="text-muted d-block"><span class="material-icons mr-1">group</span>
                                    {{ getGroupByAccess(user.id).name }} - {{
                                        getGroupByAccess(user.id).aliases.join()
                                    }}
                                    <template v-if="(user.aliases || []).length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">local_offer</span><span>{{
                                                user.aliases.join(', ')
                                            }}</span>
                                        </span>
                                    </template>
                                    <template v-if="(user.tags || []).length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">tag</span><span>{{
                                                user.tags.join(', ')
                                            }}</span>
                                        </span>
                                    </template>
                                </small>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right" @click="onDeleteUser(user.id)"
                                        v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                                <button class="btn mr-2 btn-sm btn-light float-right" @click="onEditUser(user)"
                                        v-if="ui.editable">
                                    <span class="material-icons text-warning">edit</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <hr class="my-3"/>
            <div class="row pb-3" v-if="ui.editable">
                <div class="col-5">
                    <div class="form-group">
                        <label>Vorname</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false"></button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button" v-for="unusedUser in unusedUsers"
                                            @click="onSelectDummy(unusedUser.email)">
                                            <span>{{
                                                    unusedUser.first_name + ' ' + unusedUser.last_name + ' - ' + unusedUser.email
                                                }}</span>
                                    </button>
                                </div>
                            </div>
                            <input v-model="newUser.first_name" type="text" class="form-control form-control-sm"
                                   placeholder="Vorname...">
                            <input v-model="newUser.last_name" type="text" class="form-control form-control-sm"
                                   placeholder="Nachname...">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>E-Mail</label>
                        <input v-model="newUser.email" type="text" class="form-control form-control-sm"
                               placeholder="E-Mail...">
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Prozess-Identität</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false"></button>
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
                                    v-model="newUser.identity_process_type">
                                <option value="">Bitte wählen...</option>
                                <option value="allisa/person@latest">allisa/person@latest</option>
                                <option v-for="graph in identityGraphs" :value="graph.full_namespace">
                                    {{ graph.full_namespace }}
                                </option>
                            </select>
                            <select v-if="showIdentityProcessInstanceSelect" class="form-control form-control-sm"
                                    v-model="newUser.identity_process_instance">
                                <option value="">Bitte wählen...</option>
                                <option v-for="process in identityProcessInstances" :value="process.id">
                                    {{ process.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Gruppe</label>
                        <select class="form-control form-control-sm" name="group-selection" v-model="selectedGroup">
                            <option value="">Bitte wählen...</option>
                            <option value="5c761e72-b7f0-4962-8b7e-9c534abaf4f7">Standard - default</option>
                            <option v-for="group in blueprint.groups" :value="group.id">
                                {{ group.name + ' - ' + group.aliases.join() }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Aliases</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" v-model="newUser.aliases">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-expanded="false">Demo
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_1'">
                                        demo_user_1
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_2'">
                                        demo_user_2
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_3'">
                                        demo_user_3
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_4'">
                                        demo_user_4
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_5'">
                                        demo_user_5
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_6'">
                                        demo_user_6
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_7'">
                                        demo_user_7
                                    </button>
                                    <button class="dropdown-item" type="button"
                                            @click="newUser.aliases = 'demo_user_8'">
                                        demo_user_8
                                    </button>
                                </div>
                            </div>
                        </div>
                        <span class="text-muted">Nur "a-z", "0-9" und Unterstrich. Optionale, kommaseparierte Identifikationen, welche den Benutzer zusätzlich identifizieren können. Aliases sind einzigartig innerhalb aller Benutzer.</span>
                        <div class="invalid-feedback d-block mt-0"
                             v-for="error in [...ui.validationErrors['aliases.0'] || []]">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Tags</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" v-model="newUser.tags">
                        </div>
                        <span class="text-muted">Nur "a-z", "0-9" und Unterstrich. Optionale, kommaseparierte Tags, welche den Benutzer zusätzlich identifizieren können. Mehrere Benutzer können denselben Tag nutzen.</span>
                    </div>
                </div>
                <div class="col-1 d-flex align-items-end justify-content-end">
                    <div class="row no-gutters">
                        <div v-if="mode === 'edit'" class="col-6">
                            <button class="btn btn-sm btn-outline-danger" @click="resetNewUser">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                        <div v-if="mode === 'add'" class="col-6">
                            <button class="btn btn-sm btn-outline-primary" type="button" @click="onAddUser">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                        <div v-if="mode === 'edit'" class="col-6">
                            <button class="btn btn-sm btn-outline-success" type="button" @click="onUpdateUser">
                                <span class="material-icons">save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-for="error in ([...(ui.validationErrors.identity_process_type || []), ...(ui.validationErrors.first_name || []), ...(ui.validationErrors.last_name || []), ...(ui.validationErrors.email || []), ...(ui.validationErrors['aliases.0'] || []), ...localErrors])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
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
            newUser: {
                id: '',
                identity_process_type: '',
                identity_process_instance: '',
                group_id: '',
                first_name: '',
                last_name: '',
                email: '',
                aliases: '',
                tags: ''
            },
            mode: 'add',
            editedUser: null,
            localErrors: [],
            showIdentityProcessTypeSelect: true,
            showIdentityProcessInstanceSelect: false,
            selectedUser: '',
            selectedGroup: '',
            dummyUsers: [
                {
                    first_name: 'Franziska',
                    last_name: 'Frühling',
                    email: 'fruehling@example.com',
                    aliases: 'demo_franziska',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Simon',
                    last_name: 'Sommer',
                    email: 'sommer@example.com',
                    aliases: 'demo_simon',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Hannah',
                    last_name: 'Herbst',
                    email: 'herbst@example.com',
                    aliases: 'demo_hannah',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Walter',
                    last_name: 'Winter',
                    email: 'winter@example.com',
                    aliases: 'demo_walter',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Daniel',
                    last_name: 'Diamant',
                    email: 'daniel@example.com',
                    aliases: 'demo_daniel',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Roland',
                    last_name: 'Rubin',
                    email: 'roland@example.com',
                    aliases: 'demo_roland',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Sarah',
                    last_name: 'Saphir',
                    email: 'sarah@example.com',
                    aliases: 'demo_sarah',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
                {
                    first_name: 'Olivia',
                    last_name: 'Opal',
                    email: 'olivia@example.com',
                    aliases: 'demo_olivia',
                    tags: '',
                    identity_process_type: 'allisa/person@latest',
                    identity_process_instance: '',
                    group_id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7'
                },
            ]
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs'
        ]),
        identityGraphs() {
            // Nur Process-Versionen wo es entweder die Datenfelder "first_name", "last_name" und "user"
            // oder "vorname", "nachname" und "benutzer".
            let graphs = [...this.graphs];

            return graphs.filter(function (graph) {
                let outputNames = graph.outputs.map(ele => ele.name);

                return [
                    'first_name',
                    'last_name',
                    'user'
                ].every(ele => outputNames.includes(ele)) || [
                    'vorname',
                    'nachname',
                    'benutzer'
                ].every(ele => outputNames.includes(ele));
            }).sort((a, b) => a.full_namespace < b.full_namespace ? -1 : 1);
        },
        blueprint() {
            return this.environment.blueprint;
        },
        unusedUsers() {
            let usedEmails = this.blueprint.users.map(ele => ele.email) || [];
            return this.dummyUsers.filter(ele => !usedEmails.includes(ele.email));
        },
        identityProcessInstances() {
            let processes = this.environment.blueprint.processes;
            let identityGraphsNamespaces = this.identityGraphs.map((graph) => graph.full_namespace);

            return processes.filter(function (process) {
                return identityGraphsNamespaces.includes(process.process_type);
            });
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        },
        onDeleteUser(id) {
            this.patchBlueprint('DeleteUser', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
                this.$emit('default-user-change', response.data.default_user);
            });
        },
        onEditUser(user) {
            this.newUser = {
                ...user,
                aliases: user.aliases[0],
                tags: user.tags.join(',')
            };
            if (user.identity_process_type) {
                this.showIdentityProcessTypeSelect = true;
                this.showIdentityProcessInstanceSelect = false;
            }
            else {
                if (user.identity_process_instance) {
                    this.showIdentityProcessInstanceSelect = true;
                    this.showIdentityProcessTypeSelect = false;
                }
            }
            this.selectedGroup = this.getGroupByAccess(user.id).id;
            this.mode = 'edit';
            this.editedUser = user;
        },
        onSelectDummy(email) {
            let dummyUser = this.dummyUsers.find(ele => ele.email === email);

            if (dummyUser) {
                this.selectedGroup = dummyUser.group_id;
                this.newUser = {
                    ...dummyUser,
                    aliases: dummyUser.aliases
                };
                this.showIdentityProcessTypeSelect = true;
                this.showIdentityProcessInstanceSelect = false;
            }
            else {
                this.resetNewUser();
            }
        },
        onAddUser() {
            let user = {
                ...this.newUser,
                id: uuidv4(),
                group_id: this.selectedGroup,
                aliases: this.newUser.aliases.split(',').map(ele => ele.trim()).filter(ele => ele !== ''),
                tags: this.newUser.tags.split(',').map(ele => ele.trim()).filter(ele => ele !== '')
            };

            this.patchBlueprint('StoreUser', user).then((response) => {
                this.resetNewUser();
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        onUpdateUser() {
            let user = {
                ...this.newUser,
                group_id: this.selectedGroup,
                aliases: this.newUser.aliases.split(',').map(ele => ele.trim()).filter(ele => ele !== ''),
                tags: this.newUser.tags.split(',').map(ele => ele.trim()).filter(ele => ele !== '')
            };

            this.patchBlueprint('UpdateUser', user).then((response) => {
                this.resetNewUser();
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
            this.newUser.identity_process_instance = '';
            this.newUser.identity_process_type = '';
        },
        resetNewUser() {
            this.selectedUser = '';
            this.newUser = {
                id: '',
                identity_process_type: '',
                identity_process_instance: '',
                first_name: '',
                last_name: '',
                email: '',
                aliases: '',
                tags: '',
                group_id: ''
            };
            this.showIdentityProcessTypeSelect = true;
            this.showIdentityProcessInstanceSelect = false;
            this.mode = 'add';
            this.editedUser = null;
            this.selectedGroup = '';
        },
        getGroupByAccess(userId) {
            let access = this.blueprint.group_accesses.find(ele => ele.user_id === userId);

            if (!access) {
                return {
                    id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7',
                    name: 'Standard',
                    aliases: ['default']
                };
            }

            let group = this.blueprint.groups.find(ele => ele.id === access.group_id);

            if (group) {
                return group;
            }

            return {
                id: '5c761e72-b7f0-4962-8b7e-9c534abaf4f7',
                name: 'Standard',
                aliases: ['default']
            };
        },
        getProcessName(processId) {
            let process = this.identityProcessInstances.find(function (intance) {
                return intance.id === processId;
            });
            return process.name;
        }
    },
    watch: {
        newUser: {
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
