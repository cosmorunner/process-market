<template>
    <div class="control-panels">
        <div v-if="simulation.connector_error === 403">
            <div class="alert alert-primary p-2 m-2" role="alert">
                Der gewählte Benutzer hat keinen Lese-Zugriff auf die Demo Prozess-Instanz. Wechseln Sie oben in der
                Leiste den Benutzer oder passen Sie die Benutzer-Zugriffe im Prozess an.
            </div>
        </div>
        <div v-else>
            <nav>
                <div class="nav nav-pills border-bottom" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active p-2" id="nav-situation-tab" data-toggle="tab"
                       href="#nav-situation" role="tab" aria-controls="nav-situation" aria-selected="false">Situation</a>
                    <a class="nav-item nav-link p-2" id="nav-active_actions-tab" data-toggle="tab"
                       href="#nav-active_actions"
                       role="tab" aria-controls="nav-active_actions" aria-selected="false">Aktionen</a>
                    <a class="nav-item nav-link p-2" id="nav-data-tab" data-toggle="tab" href="#nav-data"
                       role="tab" aria-controls="nav-data" aria-selected="false">
                        <span>Daten</span>
                        <span class="badge badge-light" v-if="simulation.data.length">{{ simulation.data.length }}</span>
                    </a>
                    <a class="nav-item nav-link p-2" id="nav-accesses-tab" data-toggle="tab" href="#nav-accesses" role="tab"
                       aria-controls="nav-accesses" aria-selected="false">
                        <span>Zugriffe</span>
                        <span class="badge badge-light" v-if="simulation.accesses.length">{{ simulation.accesses.length }}</span>
                    </a>
                    <a class="nav-item nav-link p-2" id="nav-history-tab" data-toggle="tab" href="#nav-history" role="tab"
                       aria-controls="nav-history" aria-selected="false">
                        <span>Verlauf</span>
                        <span class="badge badge-light" v-if="simulation.history.length">{{ simulation.history.length }}</span>
                    </a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane active py-0 panel-content-max-vh overflow-auto" id="nav-situation"
                     role="tabpanel" aria-labelledby="nav-situation-tab">
                    <template v-for="status in simulation.situation">
                        <StatusType :status-type="statusType(status.status_type_reference)"
                                    :hide-footer="true"
                                    :state-description="status.text_value"
                                    :only-for-value="status.value"
                        />
                    </template>
                    <div class="card-body px-2 py-1">
                        <small class="text-muted">Möglicherweise werden manche Daten aufgrund fehlender Rollen-Berechtigung nicht angezeigt.</small>
                    </div>
                </div>
                <div class="tab-pane py-0 panel-content-max-vh overflow-auto" id="nav-active_actions" role="tabpanel"
                     aria-labelledby="nav-active_actions-tab">
                    <template v-for="action_type in sortedActionTypes">
                        <ActionType :action-type="action_type" :hide-delete-buttons="true" collapse-rules />
                    </template>
                </div>
                <div class="tab-pane py-0 panel-content-max-vh overflow-auto" id="nav-data" role="tabpanel"
                     aria-labelledby="nav-data-tab">
                    <SimulationData :data="simulation.data" :meta-data="simulation.meta_data"/>
                </div>
                <div class="tab-pane py-0 panel-content-max-vh overflow-auto" id="nav-accesses" role="tabpanel"
                     aria-labelledby="nav-data-tab">
                    <Accesses :accesses="simulation.accesses"
                              :allisa-user-id="simulation.allisa_user_id"
                              :public-role="publicRole"
                              :environment="environment"
                    />
                </div>
                <div class="tab-pane py-0 panel-content-max-vh overflow-auto" id="nav-history" role="tabpanel"
                     aria-labelledby="nav-data-tab">
                    <History :history="simulation.history"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import StatusType from "./StatusType";
import ActionType from "./ActionType";
import SimulationData from "./SimulationData";
import History from "./History";
import utils from '../../develop-utils';
import ActionTypeCategory from "./ActionTypeCategory";
import Accesses from "./Accesses";

export default {
    components: {
        Accesses,
        ActionTypeCategory,
        StatusType,
        ActionType,
        SimulationData,
        History
    },
    methods: {
        ...utils,
        statusType(statusTypeReference) {
            for (let i = 0; i < this.status_types.length; i++) {
                if (this.status_types[i].reference === statusTypeReference) {
                    return this.status_types[i];
                }
            }

            return null;
        }
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'definition',
            'ui',
            'simulation',
            'environments',
            'active_action_type_ids',
            'inaccessible_action_type_ids'
        ]),
        // Erst aktive, dann nicht-berechtige, dann inaktive
        sortedActionTypes() {
            let that = this;
            let actionTypes = [...this.action_types];

            return actionTypes.sort(function (a, b) {
                let aValue = that.active_action_type_ids.includes(a.id) && !that.inaccessible_action_type_ids.includes(a.id) ? 2 : 0;
                let bValue = that.active_action_type_ids.includes(b.id) && !that.inaccessible_action_type_ids.includes(b.id) ? 2 : 0;

                if (that.inaccessible_action_type_ids.includes(a.id)) {
                    aValue++;
                }

                if (that.inaccessible_action_type_ids.includes(b.id)) {
                    bValue++;
                }

                return aValue < bValue;
            });
        },
        publicRole() {
            return this.definition.roles.find(ele => ele.id === this.definition.public_role_id) || null;
        },
        environment() {
            return this.environments.find(ele => ele.id === this.simulation.environment_id) || null;
        }
    },
};
</script>
