<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary btn-sm mb-2" @click="openAddEnvironmentModal" v-if="ui.editable">
                        Umgebung anlegen
                    </button>
                    <Docs class="mr-2" article="environments"/>
                </div>
                <div class="rounded-0 card mb-2" v-for="environment in sortedEnvironments">
                    <div class="card-header px-2 py-1 d-flex justify-content-between border-primary">
                        <span class="text-primary text-truncate disable-user-select">
                            <span>{{ environment.name }} </span>
                            <span class="badge badge-primary" v-if="environment.default">Standard</span>
                            <span class="material-icons" v-if="environment.public">public</span>
                        </span>
                        <div>
                            <button class="btn btn-sm btn-light p-0 mr-2" @click="copyEnvironment(environment.id)" v-if="ui.editable">
                                <span class="material-icons">content_copy</span>
                            </button>
                            <button class="btn btn-sm btn-light text-danger p-0" @click="deleteEnvironment(environment.id)" v-if="ui.editable">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body hover-pointer p-2" @click="onOpenEditModal(environment)">
                        <div v-if="environment.description">
                            <span class="text-muted">{{ environment.description || '&nbsp;' }}</span>
                        </div>
                        <div v-else>
                            <span class="text-muted"><i>Keine Beschreibung</i></span>
                        </div>
                        <div v-if="environment.initial_action_type_id" class="mb-2">
                            <span class="d-block">Initiale Aktion</span>
                            <span class="badge badge-light mr-1 mt-1">{{
                                    initialActionTypeName(environment.initial_action_type_id)
                                }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="d-block">Standard Benutzer</span>
                            <span class="badge badge-light mr-1 mt-1">{{
                                    defaultUserFullname(environment.blueprint, environment.default_user)
                                }}</span>
                        </div>
                        <div v-if="environment.query_context" class="mb-2">
                            <span class="d-block">"context"-Parameter</span>
                            <OptionBadges :value="environment.query_context"/>
                        </div>
                        <div v-if="(environment.blueprint.processes || []).length" class="mb-2">
                            <span class="d-block">Prozesse</span>
                            <div v-for="process in environment.blueprint.processes">
                        <span class="badge badge-light mr-1 mt-1">
                            <span class="material-icons">grain</span> <span>{{ process.process_type }} - {{
                                process.name
                            }}</span>
                        </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.users || []).length" class="mb-2">
                            <span class="d-block">Benutzer</span>
                            <div v-for="user in environment.blueprint.users">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span class="material-icons mx-1">person</span>
                                    <span v-if="user.identity_process_type">{{ user.identity_process_type }}</span>
                                    <span v-if="user.identity_process_instance">{{ getProcess(environment, user.identity_process_instance).name }}</span>
                                    <span> - </span>
                                    <span>{{ user.first_name + ' ' + user.last_name }}</span>
                                    <span> - </span>
                                    <span><span class="material-icons">group</span> {{
                                            getGroupByAccess(environment.blueprint, user.id).name
                                        }}</span>
                                    <template v-if="user.aliases.length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">local_offer</span><span>{{
                                                user.aliases.join(', ')
                                            }}</span>
                                        </span>
                                    </template>
                                    <template v-if="user.tags.length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">tag</span><span>{{
                                                user.tags.join(', ')
                                            }}</span>
                                        </span>
                                    </template>
                                </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.bots || []).length" class="mb-2">
                            <span class="d-block">Bots</span>
                            <div v-for="bot in (environment.blueprint.bots || []) ">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span class="material-icons mx-1">smart_toy</span>
                                    <span>allisa/bot@1.0.0</span>
                                    <span> - </span>
                                    <span>{{ bot.first_name }}</span>
                                    <template v-if="bot.aliases.length">
                                        <span class="d-inline-block ml-2">
                                            <span class="material-icons mr-1">local_offer</span><span>{{ bot.aliases.join() }}</span>
                                        </span>
                                    </template>
                                </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.groups || []).length" class="mb-2">
                            <span class="d-block">Gruppen</span>
                            <div v-for="group in environment.blueprint.groups">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span class="material-icons mx-1">group</span>
                                    <span v-if="group.identity_process_type">{{ group.identity_process_type }} - </span>
                                    <span v-if="group.identity_process_instance">{{ getProcess(environment, group.identity_process_instance).name }} - </span>
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
                                </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.relations || []).length" class="mb-2">
                            <span class="d-block">Verknüpfungen</span>
                            <div v-for="relation in environment.blueprint.relations">
                        <span class="badge badge-light mr-1 mt-1">
                            <span>{{ getProcess(environment, relation.left).name }}</span>
                            <span class="material-icons mx-1">arrow_forward_ios</span>
                            <OptionBadges :value="relation.relation_type"/>
                            <span class="material-icons mx-1">arrow_back_ios</span>
                            <span>{{ getProcess(environment, relation.right).name }}</span>
                        </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.connectors || []).length" class="mb-2">
                            <span class="d-block">Konnektoren</span>
                            <div v-for="connector in environment.blueprint.connectors">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span>{{ connector.name }} - {{ connector.identifier }}</span>
                                    <span v-if="getRequests(environment, connector.id).length">{{
                                            getRequests(environment, connector.id).length + ' - Anfrage(n)'
                                        }}</span>
                                </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.public_apis || []).length" class="mb-2">
                            <span class="d-block">Öffentliche APIs</span>
                            <div v-for="publicApi in environment.blueprint.public_apis">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span>{{ publicApiTypes[publicApi.type] }}:</span>
                                    <span>{{ publicApi.name }}</span>
                                    <span> - </span>
                                    <span>{{ publicApi.slug }}</span>
                                </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.variables || []).length" class="mb-2">
                            <span class="d-block">Variablen</span>
                            <div v-for="variable in [...environment.blueprint.variables].sort((a, b) => a.identifier > b.identifier ? 1 : -1)">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span>{{ variable.identifier }}</span>
                                    <span> - </span>
                                    <span>{{ variableTypes[variable.type] }}</span>
                                </span>
                            </div>
                        </div>
                        <div v-if="(environment.blueprint.tasks || []).length">
                            <span class="d-block">Aufgaben</span>
                            <div v-for="task in [...environment.blueprint.tasks].sort((a, b) => a.identifier > b.identifier ? 1 : -1)">
                                <span class="badge badge-light mr-1 mt-1">
                                    <span>{{ task.identifier }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-2">
                <Docs article="environments"/>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../config-utils';
import Modal from "./Modal";
import LoadingSeparator from "./LoadingSeparator";
import OptionBadges from "../utils/OptionBadges";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        OptionBadges,
        Modal,
        LoadingSeparator
    },
    data() {
        return {
            publicApiTypes: {
                list: 'Liste',
                action: 'Aktion',
                initial_action: 'Initialaktion',
                process: 'Prozess'
            },
            variableTypes: {
                TYPE_STRING: 'Zeichenkette',
                TYPE_JSON: 'JSON',
                TYPE_DOCUMENT: 'Dokument',
                TYPE_SMART_VARIABLE: 'Smart Variable',
            }
        };
    },
    computed: {
        ...mapGetters([
            'process',
            'ui',
            'environments',
            'graphs',
            'simulation',
            'action_types',
            'definition',
        ]),
        sortedEnvironments() {
            return [...this.environments].sort((a, b) => a.name > b.name ? 1 : -1);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        openAddEnvironmentModal() {
            this.storeEnvironment().then((response) => {
                this.openModal({
                    componentName: 'EditEnvironmentModal',
                    data: {
                        addMode: true,
                        environmentId: response.data.id,
                        processVersionId: this.ui.processVersionId
                    }
                });
            });
        },
        onOpenEditModal(environment) {
            this.openModal({
                componentName: 'EditEnvironmentModal',
                data: {
                    environmentId: environment.id,
                    processVersionId: this.ui.processVersionId
                }
            });
        },
        getProcess(environment, id) {
            if (id === this.simulation.default_allisa_process_id) {
                return {
                    name: 'Demo-Prozess'
                };
            }

            if (id === this.simulation.default_allisa_user_identity_id) {
                return {
                    name: 'Demo-Benutzer - Prozess-Identität'
                };
            }

            return environment.blueprint.processes.find(ele => ele.id === id) || {
                name: 'unknown'
            };
        },
        getRequests(environment, connectorId) {
            return environment.blueprint.requests.filter(ele => ele.connector_id === connectorId);
        },
        getGroupByAccess(blueprint, userId) {
            let access = blueprint.group_accesses.find(ele => ele.user_id === userId);

            if (!access) {
                return {
                    name: 'Standard',
                    identifier: 'default'
                };
            }

            let group = blueprint.groups.find(ele => ele.id === access.group_id);

            if (group) {
                return group;
            }

            return {
                name: 'Standard',
                identifier: 'default'
            };
        },
        initialActionTypeName(id) {
            let actionType = this.action_types.find(ele => ele.id === id);

            if (actionType) {
                return actionType.name;
            }

            return id;
        },
        defaultUserFullname(blueprint, defaultUser) {
            let user = blueprint.users.find(ele => ele.id === defaultUser);

            if (user) {
                return user.first_name + ' ' + user.last_name;
            }

            return 'Demo Benutzer';
        }
    }
};
</script>
