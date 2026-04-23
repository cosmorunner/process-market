<template>
    <div>
        <div class="modal" id="genericModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="genericModal"
             aria-hidden="true">
            <component :is="ui.modal.componentName" @cancel="onCancel"></component>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';

export default {
    components: {
        ExecuteActionModal: () => import("./ExecuteActionModal.vue"),
        StatusTypeModal: () => import("./StatusTypeModal.vue"),
        StateModal: () => import("./StateModal.vue"),
        ActionTypeModal: () => import("./ActionTypeModal.vue"),
        ActionRuleModal: () => import("./ActionRuleModal.vue"),
        StatusRuleModal: () => import("./StatusRuleModal.vue"),
        InputModal: () => import("./InputModal.vue"),
        BulkModalText: () => import("./BulkModalText.vue"),
        OutputModal: () => import("./OutputModal.vue"),
        RoleModal: () => import("./RoleModal.vue"),
        SimulationOptionsModal: () => import("./SimulationOptionsModal.vue")
    },
    computed: {
        ...mapGetters([
            'ui'
        ])
    },
    methods: {
        ...mapActions([
            'clearError',
            'clearValidationErrors',
            'closeModal'
        ]),
        onCancel(){
            this.clearError()
        }
    },
    created() {
        let that = this;
        $(document).one('hidden.bs.modal', '#genericModal', () => that.closeModal());
    },
    mounted() {
        $('#genericModal').modal('show');
    },
    beforeDestroy() {
        $('#genericModal').modal('hide');
    }
};
</script>
