<template>
    <div class="d-inline-block">
        <Modal v-if="modal"
               :modal="modal"
               :error-code="null"
               :error-message="''"
               :validation-errors="[]"
               :loading="false"
               :clear-error="() => null"
               @close-modal="closeModal"
        />
        <div class="d-inline-block">
            <button class="btn btn-light btn-sm" @click="openModal">
                <span class="material-icons" data-toggle="tooltip" data-placement="top" title="Aktueller Benutzer">person</span>
                <span>{{ demoAccessLabel(allisaUserId) }}</span>
                <span class="material-icons text-muted" data-toggle="tooltip" data-placement="top"
                      title="Benutzer wechseln">swap_horiz</span>
            </button>
        </div>
    </div>
</template>
<script>

import Modal from "./Modal";
import SwitchDemoUserModal from "./SwitchDemoUserModal";

export default {
    components: {
        Modal,
        SwitchDemoUserModal
    },
    props: {
        demoId: String,
        users: Array,
        allisaDemoUserId: String,
        allisaUserId: String,
        publicRoleId: String | null
    },
    data() {
        return {
            modal: null
        };
    },
    methods: {
        openModal() {
            this.modal = {
                component: SwitchDemoUserModal,
                onConfirm: this.onConfirm,
                data: {
                    demoId: this.demoId,
                    demoAccessLabel: this.demoAccessLabel,
                    groupLabel: this.groupLabel,
                    allisaDemoUserId: this.allisaDemoUserId,
                    allisaUserId: this.allisaUserId
                }
            };
        },
        closeModal() {
            this.modal = null;
        },
        demoAccessLabel(userId) {
            let user = this.users.find(ele => ele.id == userId);

            return user.first_name + ' ' + user.last_name;
        },
        groupLabel(userId) {
            let user = this.users.find(ele => ele.id == userId);

            return user.groups.map(group => group.name).join(', ');
        }
    }
};
</script>
