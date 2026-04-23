<template>
    <div class="control-panels">
        <nav>
            <div class="nav nav-pills border-bottom" id="nav-tab" role="tablist">
                <a @click="updateNavigation({nav: 'actionTypes'})"
                   :class="'nav-item nav-link p-2 ' + (ui.navigation.nav === 'actionTypes' ? 'active' : '')"
                   data-toggle="tab" role="tab" href="#">Aktionen</a>
                <a @click="updateNavigation({nav: 'statusTypes'})"
                   :class="'nav-item nav-link p-2 ' + (ui.navigation.nav === 'statusTypes' ? 'active' : '')"
                   data-toggle="tab" role="tab" href="#">Status</a>
                <a @click="updateNavigation({nav: 'outputs'})"
                   :class="'nav-item nav-link p-2 ' + (ui.navigation.nav === 'outputs' ? 'active' : '')" data-toggle="tab"
                   role="tab" href="#">Daten</a>
                <a @click="updateNavigation({nav: 'roles'})"
                   :class="'nav-item nav-link p-2 ' + (ui.navigation.nav === 'roles' ? 'active' : '')" data-toggle="tab"
                   role="tab" href="#">Rollen</a>
                <a @click="updateNavigation({nav: 'options'})"
                   :class="'nav-item nav-link p-2 ' + (ui.navigation.nav === 'options' ? 'active' : '')" data-toggle="tab"
                   role="tab" href="#">
                    <span class="material-icons">settings</span>
                </a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div v-if="ui.navigation.nav === 'actionTypes'" class="active py-0 panel-content-max-vh overflow-auto no-scrollbar">
                <ActionTypes :action_types="action_types"/>
            </div>
            <div v-if="ui.navigation.nav === 'statusTypes'" class="py-0 panel-content-max-vh overflow-auto no-scrollbar">
                <StatusTypes :status_types="status_types"/>
            </div>
            <div v-if="ui.navigation.nav === 'outputs'" class="py-0 panel-content-max-vh overflow-auto no-scrollbar">
                <Outputs :outputs="outputs" :action-type-id="null" store-method="StoreProcessTypeOutput"
                         update-method="UpdateProcessTypeOutput" store-bulk-method="StoreProcessTypeOutputBulk"
                         update-bulk-method="UpdateProcessTypeOutputBulk" :editable="ui.editable"/>
            </div>
            <div v-if="ui.navigation.nav === 'roles'" class="py-0 panel-content-max-vh overflow-auto no-scrollbar">
                <Roles :roles="roles"/>
            </div>
            <div v-if="ui.navigation.nav === 'options'" class="py-0 panel-content-max-vh overflow-auto no-scrollbar">
                <Options/>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../develop-utils';
import ActionTypes from "./ActionTypes.vue";

export default {
    components: {
        ActionTypes,
        Outputs: () => import("./Outputs.vue"),
        StatusTypes: () => import("./StatusTypes.vue"),
        Roles: () => import("./Roles.vue"),
        Options: () => import("./Options.vue"),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions)
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'ui',
            'definition',
            'outputs',
            'roles'
        ])
    },
};
</script>
