<template>
    <div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0" v-if="userAccesses.length">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Benutzer</span>
            </div>
            <div class="card-body p-2 border-bottom">
                <div class="row no-gutters" v-for="access in userAccesses">
                    <div class="col-1">
                        <span class="material-icons" v-if="allisaUserId === access.recipient_id"
                              data-toggle="tooltip" data-placement="top" title="Aktueller Benutzer">person</span>
                    </div>
                    <div class="col-5">
                        <span>
                            <span>{{ access.recipient_name }}</span>
                            <span class="d-block text-muted"><span
                                class="material-icons">group</span> {{ userGroupName(access.recipient_id) }}</span>
                        </span>
                    </div>
                    <div class="col-5">
                        <span>{{ access.role_name }}</span>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-sm btn-success" v-if="allisaUserId !== access.recipient_id"
                                data-toggle="tooltip" data-placement="top" title="Zu diesem Benutzer wechseln"
                                @click="handleSwitchUser(access.recipient_id)">
                            <span class="material-icons">person</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0" v-if="groupAccesses.length">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Gruppen</span>
            </div>
            <div class="card-body p-2 border-bottom">
                <div class="row no-gutters" v-for="access in groupAccesses">
                    <div class="col-1 pb-1"></div>
                    <div class="col-5 pb-1 border-bottom">
                        <span>{{ access.recipient_name }}</span>
                    </div>
                    <div class="col-6 pb-1 border-bottom">
                        <span>{{ access.role_name }}</span>
                    </div>
                    <div class="col-12 mt-1" v-if="groupUsers(access.recipient_id).length">
                        <div class="row no-gutters" v-for="groupUser in groupUsers(access.recipient_id)">
                            <div class="col-1">
                            <span class="material-icons" v-if="allisaUserId === groupUser.id"
                                  data-toggle="tooltip" data-placement="top" title="Aktueller Benutzer">person</span>
                            </div>
                            <div class="col-10">
                                {{ groupUser.first_name + ' ' + groupUser.last_name }}
                            </div>
                            <div class="col-1">
                                <button class="btn btn-sm btn-success" v-if="allisaUserId !== groupUser.id"
                                        data-toggle="tooltip" data-placement="top" title="Zu diesem Benutzer wechseln"
                                        @click="handleSwitchUser(groupUser.id)">
                                    <span class="material-icons">person</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="row no-gutters">
                            <div class="col-12">
                                <i>Diese Gruppe hat keine Benutzer.</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
                <span>Öffentlicher Zugriff</span>
            </div>
            <div class="card-body p-2 border-bottom">
                <div class="row no-gutters">
                    <div class="col-12" v-if="publicRole">
                        <span><span class="material-icons">group</span> {{ publicRole.name }}</span>
                    </div>
                    <div class="col-12" v-else>
                        <span>Kein Zugriff mit einer öffentlichen Rolle.</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import utils from "../../develop-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";

export default {
    props: {
        accesses: Array,
        allisaUserId: String,
        publicRole: null | Object,
        environment: null | Object
    },
    computed: {
        ...mapGetters([
            'inaccessible_action_type_ids',
            'active_action_type_ids',
            'active_state_ids',
            'simulation',
            'ui'
        ]),
        userAccesses() {
            return this.accesses.filter(ele => ele.recipient_type === 'User');
        },
        groupAccesses() {
            return this.accesses.filter(ele => ele.recipient_type === 'Group');
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        userGroupName(userId) {
            if (!this.environment) {
                return 'Standard';
            }

            let groupAccess = this.environment.blueprint.group_accesses.find(ele => ele.user_id === userId);

            // Standardgemäß ist der Demo-Benutzer in der "Standard"-Gruppe.
            if (!this.environment || !groupAccess) {
                return 'Standard';
            }

            let group = this.environment.blueprint.groups.find(ele => ele.id === groupAccess.group_id);

            if (group) {
                return group.name;
            }

            return 'Standard';
        },
        groupUsers(groupId) {
            if (!this.environment) {
                return [];
            }

            let userIds = this.environment.blueprint.group_accesses
                .filter(ele => ele.group_id === groupId)
                .map(ele => ele.user_id);

            return this.environment.blueprint.users.filter(ele => userIds.includes(ele.id));
        },
        handleSwitchUser(userId) {
            $('[data-toggle="tooltip"]').tooltip('dispose');

            this.switchUser(userId)
                .then(response => this.updateSimulation(response.data))
                .then(() => {
                    this.setViewMode('simulation');
                    $('[data-toggle="tooltip"]').tooltip();
                });
        }
    }
};
</script>
