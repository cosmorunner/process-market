<template xmlns="http://www.w3.org/1999/html">
    <div class="row justify-content-center flex-grow-1">
        <div :class="{'col-sm-12 col-md-6 col-lg-4': !isCollapse, 'col-1': isCollapse}" class="px-0">
            <div id="control-panel" :class="{'h-100': isCollapse}">
                <Modal v-if="ui.modal.open"></Modal>
                <div :class="{'h-100': isCollapse}" class="card rounded-0 border-top-0 border-bottom-0">
                    <div class="card-header p-2" :class="{'border-bottom-0': !isCollapse}">
                        <div class="container-fluid p-0">
                            <div class="row" v-if="!isCollapse">
                                <div class="col">
                                    <div class="row">
                                        <div class="col-5 d-flex justify-content-start">
                                            <a :href="authorProfilePath"
                                               class="text-truncate text-nowrap">{{ processProp.author_name }}</a>
                                        </div>
                                        <div class="col-7 d-flex justify-content-end">
                                            <div>
                                                <span>
                                                    <a :href="configUrl('ActionTypes', 'Components', ui.navigation.details.model)"
                                                       class="mr-2 text-nowrap">
                                                        <span>Konfiguration</span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div>
                                                <span v-if="!simulation.running && canCompleteVersion">
                                                    <a :href="ui.urls.complete" class="text-nowrap">
                                                        <span>Fertigstellen</span>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr v-if="!isCollapse">
                            <div class="row no-gutters">
                                <div :class="simulation.running
                                        ? 'col'
                                        : (isCollapse
                                            ? 'col justify-content-center'
                                            : 'col justify-content-between d-flex align-items-center')">
                                    <div v-if="!isCollapse && !simulation.running">
                                        <span class="text-truncate pr-2 border-loading">
                                            <span>{{ processProp.title }}</span>
                                            <span v-if="!ui.editable">
                                                <span class="badge badge-secondary">
                                                    <span class="material-icons">visibility</span>
                                                    <span>Lese-Ansicht</span>
                                                </span>
                                            </span>
                                        </span>
                                    </div>
                                    <div :class="simulation.running ? 'd-flex justify-content-between': ''">
                                        <SwitchSimulationUser :allisa-demo-user-id="allisaDemoUserId"
                                                              @switch-user="handleSwitchUser"
                                                              v-if="simulation.running && !simulation.starting && !simulation.stopping && !isCollapse"/>
                                        <span
                                            v-if="simulation.running && !simulation.allisa_id && !simulation.connector_error"
                                            class="pr-2 border-loading"
                                            :class="{'text-truncate': !isCollapse, 'text-justify': isCollapse}">
                                            Initialaktion wird ausgeführt...
                                        </span>
                                        <div v-if="!simulation.running && !simulation.starting" class="d-inline-block">
                                            <template v-if="ui.editable && !isCollapse">
                                                <div class="mr-2 d-inline-block">
                                                    <button :disabled="!ui.enabledUndo" class="btn btn-sm btn-light"
                                                            type="button" @click="undo">
                                                        <span class="material-icons">undo</span>
                                                    </button>
                                                    <button :disabled="!ui.enabledRedo" class="btn btn-sm btn-light"
                                                            type="button" @click="redo">
                                                        <span class="material-icons">redo</span>
                                                    </button>
                                                </div>
                                            </template>
                                            <div v-if="!simulation.running && !simulation.starting && !ui.errorCode"
                                                 aria-label="..." class="btn-group btn-group-sm" role="group">
                                                <div aria-label="Demo starten" class="btn-group" role="group">
                                                    <button
                                                        class="btn btn-success btn-sm d-flex align-items-center justify-content-between"
                                                        @click="openSimulationOptions(organisationId)">
                                                        <span class="material-icons mr-2">play_arrow</span>
                                                        <span>{{ isCollapse ? 'Demo' : 'Demo starten' }}</span>
                                                    </button>
                                                    <a :href="demoUrl" class="btn btn-sm btn-outline-success">
                                                        <span class="material-icons">open_in_new</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <button v-if="(simulation.starting || simulation.stopping) && !ui.errorCode"
                                                class="btn btn-warning btn-sm d-flex align-items-center justify-content-between"
                                                disabled>
                                            <span class="material-icons mr-2">settings</span>
                                            <span>Laden...</span>
                                        </button>
                                        <button v-if="ui.errorCode"
                                                class="btn btn-danger btn-sm d-inline-block align-items-center justify-content-between"
                                                @click="clearError">
                                            <span class="material-icons mr-2">priority_high</span>
                                            <span>{{ ui.errorCode === 422 ? 'Eingabefehler' : 'Error' }}</span>
                                        </button>
                                        <div
                                            v-if="simulation.running && !simulation.starting && !simulation.stopping && !ui.errorCode && userId === simulation.user_id"
                                            class="d-inline-block">
                                            <div class="d-flex">
                                                <a :href="demoUrl" class="btn btn-sm btn-outline-primary mr-1">
                                                    <span class="material-icons">open_in_new</span>
                                                    <span v-if="!isCollapse">Allisa Plattform</span>
                                                </a>
                                                <button
                                                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-between"
                                                    @click="stopSim()">
                                                    <span class="material-icons mr-1">stop</span>
                                                    <span>Stop</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="!isCollapse">
                        <LoadingSeparator :clear-error="clearError" :ui="ui"/>
                    </div>
                    <div id="control-content" class="card-body p-0 ">
                        <div v-if="!isCollapse">
                            <Panels v-if="!ui.navigation.details.type"/>
                            <ActionTypeDetail v-if="ui.navigation.details.type === 'action'"
                                              :action-type-id="ui.navigation.details.model"/>
                            <StatusTypeDetail v-if="ui.navigation.details.type === 'status'"
                                              :status-type-id="ui.navigation.details.model"/>
                            <StartDetail v-if="ui.navigation.details.type === 'start'"/>
                            <StateDetail v-if="ui.navigation.details.type === 'state'"
                                         :state-id="ui.navigation.details.model"/>
                            <ActionRuleDetail v-if="ui.navigation.details.type === 'action-rule-edge'"
                                              :action-rule-id="ui.navigation.details.model"/>
                            <StatusRuleDetail v-if="ui.navigation.details.type === 'status-rule-edge'"
                                              :status-rule-id="ui.navigation.details.model"/>
                            <InitialStateDetail v-if="ui.navigation.details.type === 'initial-state-edge'"
                                                :initial-state="ui.navigation.details.model"/>
                            <SimulationDetail v-if="ui.navigation.details.type === 'simulation'"/>
                            <RoleDetail v-if="ui.navigation.details.type === 'role'"
                                        :role-id="ui.navigation.details.model"/>
                        </div>
                    </div>
                    <div class="card-footer bg-light px-2 py-1 d-flex"
                         :class="{'justify-content-between': !isCollapse, 'justify-content-end': isCollapse}">
                        <div v-if="!isCollapse">
                            <span class="text-muted">
                                {{ processProp.namespace }}/{{ processProp.identifier }}@{{ version }}
                            </span>
                            <Docs article="rules"/>
                        </div>
                        <div>
                            <button id="toggle-expand-collapse"
                                    class="d-none d-lg-block btn btn-block btn-sm btn-light px-2 py-0"
                                    @click="toggleSidebar">
                                <span :class="{'collapse-icon': isCollapse, 'expand-icon': !isCollapse}"
                                      class="material-icons">double_arrow</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div :class="{'col-md-6 col-lg-8': !isCollapse, 'col-11': isCollapse}" class="d-none d-md-block px-0">
            <div id="cyGraph" class="main-vh"></div>
        </div>
    </div>
</template>

<script>

import ActionTypeDetail from "./ActionTypeDetail.vue";
import Docs from "../utils/Docs";
import LoadingSeparator from "../develop/LoadingSeparator";
import Modal from "./Modal";
import Panels from "./Panels";
import StatusTypeDetail from "./StatusTypeDetail.vue";
import utils from '../../develop-utils';
import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';

export default {
    components: {
        Docs,
        LoadingSeparator,
        Panels,
        Modal,
        ActionTypeDetail,
        StatusTypeDetail,
        StartDetail: () => import("./StartDetail.vue"),
        StateDetail: () => import("./StateDetail.vue"),
        ActionRuleDetail: () => import("./ActionRuleDetail.vue"),
        StatusRuleDetail: () => import("./StatusRuleDetail.vue"),
        InitialStateDetail: () => import("./InitialStateDetail.vue"),
        SimulationDetail: () => import("./SimulationDetail.vue"),
        SwitchSimulationUser: () => import("./SwitchSimulationUser.vue"),
        RoleDetail: () => import("./RoleDetail.vue"),
    },
    props: {
        authorName: String,
        userId: String,
        organisationId: String | null,
        authorProfilePath: String,
        processProp: Object,
        processVersion: Object,
        runningSimulation: Object | null,
        version: String,
        environmentsProp: Array,
        urls: Object,
        allisaDemoUserId: String,
        allisaDemoIdentityId: String,
        canCompleteVersion: Boolean,
        canUpdateVersion: Boolean,
        canUpdateProcess: Boolean,
        enabledUndo: Boolean,
        enabledRedo: Boolean,
        sidebarCollapseProp: Boolean,
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        handleSwitchUser(userId) {
            this.switchUser(userId).then(response => this.updateSimulation(response.data)).then(() => this.setViewMode('simulation'));
        },
        toggleSidebar() {
            this.isCollapse = !this.isCollapse;
            axios.patch(this.ui.urls.user_toggle_sidebar).catch(error => console.log(error));
        },
        editActionType(actionType) {
            cy.app.openModal({
                componentName: 'ActionTypeModal',
                data: {
                    actionTypeId: actionType.modelId
                }
            });
        },
        copyActionType(actionType) {
            cy.app.onCopyActionType(actionType.modelId);
        },
        newActionRule(actionType) {
            cy.app.openModal({
                componentName: 'ActionRuleModal',
                data: {
                    statusTypeId: actionType.statusTypeId,
                    actionTypeId: actionType.modelId
                }
            });
        },
        newStatusRule(actionType) {
            cy.app.openModal({
                componentName: 'StatusRuleModal',
                data: {
                    statusTypeId: actionType.statusTypeId,
                    actionTypeId: actionType.modelId
                }
            });
        },
        deleteActionType(actionType) {
            cy.app.onDeleteActionType(actionType.modelId);
        },
        editState(state) {
            cy.app.openModal({
                componentName: 'StateModal',
                data: {
                    stateId: state.modelId,
                    statusTypeId: state.statusTypeId
                }
            });
        },
        deleteState(state) {
            cy.app.onDeleteState(state.statusTypeId, state.modelId);
        }
    },
    data() {
        return {
            // Wird genutzt um Positionsänderungen der Graph-Nodes zu ermittelt. Bei einer Änderung
            // wird der Graph gespeichert.
            layoutCenterPosition: null,
            isCollapse: this.sidebarCollapseProp,
        };
    },
    computed: {
        ...mapGetters([
            'inaccessible_action_type_ids',
            'active_action_type_ids',
            'active_state_ids',
            'action_types',
            'definition',
            'environments',
            'process',
            'roles',
            'simulation',
            'status_types',
            'ui',
            'graphs_output_names'
        ]),
        userAccesses() {
            // Benutzer-Ids ermittelt die direkten Zugriff haben
            let accesses = {};
            let userAccesses = this.simulation.accesses.filter(ele => ele.recipient_type === 'User');

            for (let m = 0; m < userAccesses.length; m++) {
                accesses[userAccesses[m].recipient_id] = [userAccesses[m].role_name];
            }

            let environment = this.environments.find(ele => ele.id === this.simulation.environment_id);
            let groupAccesses = this.simulation.accesses.filter(ele => ele.recipient_type === 'Group');

            // Für jede hinzugefügte Grupep die Benutzer emittelt und das directUserAccesses-Array erweitern,
            // bzw. den role_names-Eintrag mit der Rolle der Gruppe erweitern.
            for (let i = 0; i < groupAccesses.length; i++) {
                let groupMembers = environment.blueprint.group_accesses.filter(ele => ele.group_id === groupAccesses[i].recipient_id);

                for (let k = 0; k < groupMembers.length; k++) {
                    if (accesses.hasOwnProperty(groupMembers[k].user_id)) {
                        accesses[groupMembers[k].user_id] = [
                            ...accesses[groupMembers[k].user_id],
                            groupAccesses[i].role_name
                        ];
                    }
                    else {
                        accesses[groupMembers[k].user_id] = [groupAccesses[i].role_name];
                    }
                }
            }

            return accesses;
        },
        demoUrl() {
            return this.ui.urls.demo + '?ref=' + btoa(location.href);
        }
    },
    created() {
        this.setDefinition(this.processVersion.definition);
        this.setEnvironments(this.environmentsProp);
        this.setDemoData(this.processVersion.demo_data);
        this.setProcess(this.processProp);

        this.setUiState({
            processVersionId: this.processVersion.id,
            demoUserIdentityId: this.allisaDemoIdentityId,
            urls: this.urls,
            editable: this.canUpdateVersion,
            userSettings: this.userSettings,
            enabledUndo: this.enabledUndo,
            enabledRedo: this.enabledRedo
        });
    },
    watch: {
        'ui.navigation': function (newVal) {
            this.updateUrl(newVal);
        }
    },
    mounted() {
        this.initCy(this.processVersion);
        this.initNavigation('develop');
        this.highlightDetailAction();

        // Keine Simulation
        if (!this.runningSimulation) {
            this.setViewMode('viewing');
        }

        // Simulation fortsetzen
        if (this.runningSimulation) {
            this.initSimulation(this.runningSimulation);
            this.showElementDetails({
                'type': 'simulation',
                'model': null
            });

            this.setViewMode('simulation');

            // Falls noch keine Allisa-Id existiert (Initialaktion wird noch ausgeführt),
            // alle 2.5 Sekunden versuchen.
            if (!this.runningSimulation.allisa_id && this.runningSimulation.connector_error === null) {
                let that = this;
                let interval = setInterval(fetch, 2500);

                function fetch() {
                    that.fetchSimulation().then(response => {
                        if (response.data.finished_at) {
                            that.stopSim();
                        }
                        if (response.data.allisa_id || !that.simulation.running) {
                            clearInterval(interval);
                        }
                    }).catch(function () {
                        clearInterval(interval);
                    });
                }
            }
        }

        $(this.$el).find('[data-toggle="tooltip"]').tooltip();
    }
};
</script>
