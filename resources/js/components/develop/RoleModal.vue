<template>
    <KeepAlive include="SaveRoleModal">
        <component :is="componentName" :data="data" @open-conditions="openConditions" @open-role="openRole"
                   @save-conditions="saveConditions"></component>
    </KeepAlive>
</template>

<script>

import {mapGetters} from 'vuex';
import SaveRoleModal from "./SaveRoleModal";
import PermissionConditionsModal from "./PermissionConditionsModal";

export default {
    components: {
        SaveRoleModal,
        PermissionConditionsModal
    },
    data() {
        return {
            data: {},
            componentName: 'SaveRoleModal',
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ])
    },
    methods: {
        openConditions(permission) {
            this.componentName = 'PermissionConditionsModal';
            this.data.permission = permission;
        },
        openRole() {
            this.componentName = 'SaveRoleModal';
            this.data = {};
        },
        saveConditions(permission) {
            this.componentName = 'SaveRoleModal';
            this.data = permission;
        }
    }
};
</script>
