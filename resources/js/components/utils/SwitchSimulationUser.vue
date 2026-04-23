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
                <span>{{ simulationAccessLabel(allisaUserId) }}</span>
                <span class="material-icons text-muted" data-toggle="tooltip" data-placement="top"
                      title="Benutzer wechseln">swap_horiz</span>
            </button>
        </div>
    </div>
</template>
<script>

import Modal from "./Modal";
import SwitchSimulationUserModal from "./SwitchSimulationUserModal";

export default {
    components: {
        Modal,
        SwitchSimulationUserModal
    },
    props: {
        simulationId: String,
        allisaIdProp: String,
        roles: Array,
        users: Array,
        blueprint: Object | null,
        allisaDemoUserId: String,
        allisaUserId: String,
        publicRoleId: String | null
    },
    data() {
        return {
            modal: null,
            allisaId: this.allisaIdProp,
            intervalId: null
        };
    },
    methods: {
        openModal() {
            this.modal = {
                component: SwitchSimulationUserModal,
                onConfirm: this.onConfirm,
                data: {
                    blueprint: this.blueprint,
                    roles: this.roles,
                    simulationId: this.simulationId,
                    simulationAccessLabel: this.simulationAccessLabel,
                    groupLabel: this.groupLabel,
                    allisaDemoUserId: this.allisaDemoUserId,
                    allisaUserId: this.allisaUserId
                }
            };
        },
        closeModal() {
            this.modal = null;
        },
        simulationAccessLabel(userId) {
            let user = this.users.find(ele => ele.id == userId);

            return user.first_name + ' ' + user.last_name;
        },
        groupLabel(userId) {
            let user = this.users.find(ele => ele.id == userId);

            return user.groups.map(group => group.name).join(', ');
        },
        fetchAllisaId() {
            let that = this;

            axios.get('/api/simulations/' + this.simulationId + '/sync-allisa-id').then(function (response) {
                that.allisaId = response.data.allisa_id || null;

                // Sobald die Allisa Id synchronisiert wurde, wird das Interval entfernt.
                if (that.allisaId) {
                    window.clearInterval(that.intervalId);
                }
            });
        }
    },
    mounted() {
        // Falls die Simulation mit einer Initialaktion startet wird alle 5 Sekunden geprüft,
        // ob diese bereits ausgeführt wurde. Falls ja, wird die Prozess-Id in der Simulationstabelle gespeichert.
        if (!this.allisaId) {
            this.fetchAllisaId();
            this.intervalId = window.setInterval(this.fetchAllisaId, 5000);
        }

    }
};
</script>
