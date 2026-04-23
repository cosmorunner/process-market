<template>
    <div class="control-panel-roles">
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0" v-if="ui.editable">
            <div class="card-header bg-white d-flex justify-content-between px-2 py-1 border-bottom-0">
                <button class="btn btn-sm btn-outline-primary" @click="handleOpenModal">
                    <span class="material-icons">add</span> Hinzufügen
                </button>
                <Docs article="roles"/>
            </div>
        </div>
        <template v-for="role in roles">
            <Role :role="role" :roles-count="roles.length"/>
        </template>
        <div class="card mb-3 rounded-0 border-0" v-if="!roles.length">
            <div class="card-body px-2 py-1">
                <span>Keine Rollen angelegt.</span>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import Role from "./Role";
import {reduxActions} from '../../store/develop-and-config';
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        Role
    },
    props: {
        roles: Array
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        handleOpenModal() {
            this.openModal({
                componentName: 'RoleModal',
                data: {
                    method: 'StoreRole'
                }
            });
        },
    }
};
</script>
