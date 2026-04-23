<template>
    <div class="row">
        <div class="col-12" v-if="action_type_id">
            <LoadingSeparator :ui="ui" :clear-error="clearError"/>
            <div class="row mb-2">
                <div class="col-5">
                    <div class="d-flex justify-content-between">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button @click="updateNavigation({sub: 'Components'})"
                                   :class="'nav-link ' + (ui.navigation.sub === 'Components' ? 'active' : '')"
                                   type="button">Komponenten</button>
                            </li>
                            <li class="nav-item">
                                <button @click="updateNavigation({sub: 'Processors'})"
                                   :class="'nav-link ' + (ui.navigation.sub === 'Processors' ? 'active' : '')"
                                   type="button">Prozessoren</button>
                            </li>
                            <li class="nav-item">
                                <a @click="updateNavigation({sub: 'Javascript'})"
                                   :class="'nav-link ' + (ui.navigation.sub === 'Javascript' ? 'active' : '')"
                                   href="#">JavaScript</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-7 d-flex">
                    <button class="btn btn-sm bg-white border px-1 py-0 text-primary text-nowrap" @click="onToggleActionTypeFullWidth" data-toggle="tooltip" title="Fixierte Aktionsbreite" v-if="actionType && !actionType.full_width">
                        <span class="material-icons ">navigate_next</span>
                        <span class="material-icons mi-1-5x">width_normal</span>
                        <span class="material-icons ">navigate_before</span>
                    </button>
                    <button class="btn btn-sm bg-white border px-1 py-0 text-primary text-nowrap" @click="onToggleActionTypeFullWidth" data-toggle="tooltip" title="Dynamische Aktionsbreite (100% Bildschirmbreite)" v-if="actionType && actionType.full_width">
                        <span class="material-icons ">navigate_before</span>
                        <span class="material-icons mi-1-5x">width_full</span>
                        <span class="material-icons ">navigate_next</span>
                    </button>
                    <select class="form-control ml-3" style="height: inherit;" id="action_type_id" v-model="action_type_id">
                        <option :value="actionType.id" v-for="actionType in sortedActionTypes">{{ actionType.name }}</option>
                    </select>
                    <div class="align-content-center m-2">
                        <Docs v-if="ui.navigation.sub === 'Components'" article="action-components"/>
                        <Docs v-if="ui.navigation.sub === 'Processors'" article="action-processors"/>
                        <Docs v-if="ui.navigation.sub === 'Javascript'" article="action-javascript"/>
                    </div>
                </div>
            </div>
            <Components v-if="ui.navigation.sub === 'Components' && actionType" :action-type="actionType"/>
            <Processors v-if="ui.navigation.sub === 'Processors' && actionType" :action-type="actionType"/>
            <ActionJavascript v-if="ui.navigation.sub === 'Javascript' && actionType" :action-type="actionType"/>
        </div>
        <div class="col-12" v-else>
            <span class="text-muted">Erstellen Sie zunächst bei <a :href="ui.urls.develop">"Regeln & Daten"</a>
                eine Aktion: Rechtsklick auf die freie Graphen-Fläche.</span>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../config-utils';
import LoadingSeparator from "./LoadingSeparator";
import Components from "./Components";
import Processors from "./Processors";
import Docs from "../utils/Docs";
import ActionJavascript from "./action_javascript/Javascript.vue";

export default {
    components: {
        ActionJavascript,
        Docs,
        Processors,
        Components,
        LoadingSeparator
    },
    data() {
        return {
            action_type_id: null,
            components: [
                'ActionTypes',
                'Processors',
                'Javascript'
            ]
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
        ]),
        actionType() {
            return this.action_types.find(ele => ele.id === this.action_type_id);
        },
        sortedActionTypes() {
            let actionTypes = [...this.action_types];

            return actionTypes.sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1);
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        onToggleActionTypeFullWidth() {
            this.updateActionType({...this.actionType, full_width: !this.actionType.full_width}).then(function(){
                $('[data-toggle="tooltip"]').tooltip('dispose').tooltip()
            });
        },
        updateActionType(actionType) {
            return this.patchDefinition('UpdateActionType', actionType).catch(() => {
            });
        },
    },
    watch: {
        action_type_id(newVal) {
            this.updateNavigation({
                detail: newVal,
                lastViewedModelOfType: {actionType: newVal}
            });
        }
    },
    mounted() {
        let subNav = 'Components';
        let lastViewedId = this.ui.navigation.lastViewedModelOfType.actionType || '';

        if (this.ui.navigation.sub && this.components.includes(this.ui.navigation.sub)) {
            subNav = this.ui.navigation.sub;
        }

        let detail = this.ui.navigation.detail;
        let actionType = this.action_types.find(ele => ele.id === detail);

        if (actionType) {
            this.action_type_id = actionType.id;
        }
        else if (lastViewedId && this.action_types.find(ele => ele.id === lastViewedId)) {
            this.action_type_id = this.action_types.find(ele => ele.id === lastViewedId).id;
        }
        else if (this.action_types.length) {
            this.action_type_id = this.action_types[0].id;
        }

        this.updateNavigation({
            detail: this.action_type_id,
            sub: subNav,
            lastViewedModelOfType: {actionType: this.action_type_id}
        });
    }
};
</script>
