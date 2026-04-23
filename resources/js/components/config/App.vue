<template>
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col px-0">
                <div :class="ui.userSettings.config_app_full_width ? 'container-fluid' : 'container'">
                    <div class="row mb-2 pt-4">
                        <div class="col-12 d-flex justify-content-between">
                            <div>
                                <h3>
                                    <a :href="authorProfilePath">{{ processProp.author_name }}</a>
                                    <span class="material-icons mi-1-5x mx-1 text-muted">chevron_right</span>
                                    <span>{{ processProp.title }}</span>
                                    <span class="badge badge-secondary" v-if="!ui.editable">
                                        <span class="material-icons">visibility</span>
                                        <span>Lese-Ansicht</span>
                                    </span>
                                </h3>
                            </div>
                            <div>
                                <Docs article="config"/>
                                <template v-if="!simulationId">
                                    <button type="button" class="btn btn-sm btn-outline-primary" @click="undo" :disabled="!ui.enabledUndo">
                                        <span class="material-icons">undo</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" @click="redo" :disabled="!ui.enabledRedo">
                                        <span class="material-icons">redo</span>
                                    </button>
                                </template>
                                <a :href="developUrl()" class="ml-2 btn btn-sm btn-outline-primary">
                                    Regeln & Daten
                                </a>
                                <a :href="demoUrl" class="btn btn-sm btn-outline-primary">
                                    {{ simulationId ? 'Demo fortsetzen' : 'Demo' }}
                                </a>
                                <a :href="urls.complete" class="btn btn-sm btn-outline-primary" v-if="!simulationId && canCompleteVersion">
                                    Fertigstellen
                                </a>
                                <a :href="urls.settings" class="btn btn-sm btn-outline-primary" v-if="canUpdateProcess">
                                    <span class="material-icons">settings</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row my-4" v-if="simulationId">
                        <div class="col">
                            <div class="card">
                                <div class="card-header py-1">Sie haben eine laufende Prozess-Demo.</div>
                                <div class="card-body d-flex py-3">
                                    <a :href="ui.urls.develop" class="btn btn-sm btn-outline-primary mr-1">In Regeln &
                                        Daten fortsetzen</a>
                                    <a :href="demoUrl" class="btn btn-sm btn-outline-primary mr-4">In der Allisa
                                        Plattform fortsetzen</a>
                                    <StopSimulationButton :simulation-id="simulationId"></StopSimulationButton>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div id="config-app" class="col-12">
                            <Modal v-if="ui.modal.open"></Modal>
                            <Navigation :panelName="ui.navigation.nav" @navigation-update="onNavigationUpdate"/>
                            <div class="container-fluid bg-white border border-top-0 p-3">
                                <component :is="ui.navigation.nav" v-if="definition"/>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-between">
                        <div class="col">
                            <span class="text-muted">
                                <span>{{ processProp.namespace }}/{{ processProp.identifier }}@{{ processVersion.version }}</span>
                            </span>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <!-- Full width -->
                            <button class="btn btn-sm bg-white border p-1" @click="onToggleFullWidth" data-toggle="tooltip" title="Maximale Breite für Konfiguration"
                                    v-if="!ui.userSettings.config_app_full_width">
                                <span class="material-icons text-dark">navigate_before</span>
                                <span class="material-icons text-dark">monitor</span>
                                <span class="material-icons text-dark">navigate_next</span>
                            </button>
                            <!-- Fixed width -->
                            <button class="btn btn-sm bg-white border p-1" @click="onToggleFullWidth" v-else data-toggle="tooltip" title="Fixierte Breite für Konfiguration">
                                <span class="material-icons text-dark">navigate_next</span>
                                <span class="material-icons text-dark">monitor</span>
                                <span class="material-icons text-dark">navigate_before</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import Navigation from "./Navigation";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        Navigation,
        StopSimulationButton: () => import("./StopSimulationButton.vue"),
        Modal: () => import("./Modal.vue"),
        ActionTypes: () => import("./ActionTypes.vue"),
        ActionTypeCategories: () => import("./ActionTypeCategories.vue"),
        StatusTypes: () => import("./StatusTypes.vue"),
        Lists: () => import("./Lists.vue"),
        MenuItems: () => import("./MenuItems.vue"),
        RelationTypes: () => import("./RelationTypes.vue"),
        Templates: () => import("./Templates.vue"),
        Environments: () => import("./Environments.vue"),
        Events: () => import("./Events.vue"),
        Listeners: () => import("./Listeners.vue"),
        Javascript: () => import("./Javascript.vue"),
    },
    props: {
        authorProfilePath: String,
        processProp: Object,
        processVersion: Object,
        authorName: String,
        defaultAllisaProcessId: String,
        defaultAllisaUserIdentityId: String,
        urls: Object,
        environmentsProp: Array,
        simulationId: String,
        userSettings: Object,
        editable: Boolean,
        canCompleteVersion: Boolean,
        canUpdateVersion: Boolean,
        canUpdateProcess: Boolean,
        enabledUndo: Boolean,
        enabledRedo: Boolean,
        plugins: Object,
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onNavigationUpdate(part, value) {
            this.updateNavigation({[part]: value})
        },
        onToggleFullWidth() {
            this.updateFullWidth(!this.ui.userSettings.config_app_full_width).then(function(){
                $('[data-toggle="tooltip"]').tooltip('dispose').tooltip()
            });
        },
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
            'process',
            'environments',
            'simulation',
            'relation_types_with_single_process',
            'graphs'
        ]),
        demoUrl() {
            return this.ui.urls.demo + '?ref=' + btoa(location.href);
        }
    },
    watch: {
        'ui.navigation': function (newVal) {
            this.updateUrl(newVal);
        }
    },
    created() {
        this.initNavigation();
        this.setDefinition(this.processVersion.definition);
        this.setEnvironments(this.environmentsProp);
        this.setProcess(this.processProp);
        this.setDefaultAllisaProcessId(this.defaultAllisaProcessId);
        this.setDefaultAllisaUserIdentityId(this.defaultAllisaUserIdentityId);

        this.setUiState({
            processVersionId: this.processVersion.id,
            urls: this.urls,
            editable: this.canUpdateVersion,
            userSettings: this.userSettings,
            enabledUndo: this.enabledUndo,
            enabledRedo: this.enabledRedo,
            plugins: this.plugins
        });
    }
};
</script>

<style>

html {
    overflow-y: scroll;
}

</style>
