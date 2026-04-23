<template>
    <div class="dropdown d-inline-block">
        <!-- Der aktuelle Benutzer hat Zugriff und es gibt weitere Benutzer  -->
        <template v-if="environmentUsers.filter(id => id !== simulation.allisa_user_id).length">
            <button class="btn btn-block btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    :title="simulationAccessLabel(simulation.allisa_user_id)">
                <span class="material-icons" data-toggle="tooltip" data-placement="top" title="Aktueller Benutzer">person</span>
                <span class="text-truncate">{{ simulationAccessLabel(simulation.allisa_user_id) }}</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <button type="button" class="dropdown-item" @click="$emit('switch-user', user.id)"
                        v-for="user in environmentUsers.filter(id => id !== simulation.allisa_user_id)">
                    {{ simulationAccessLabel(user.id) }}
                </button>
            </div>
        </template>
        <!-- Der aktuelle Benutzer hat KEINEN Zugriff doch es kann zu einem anderen Benutzer gewechselt werden.  -->
        <template v-else-if="simulation.connector_error === 403 && environmentUsers.length">
            <button class="btn btn-block btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton1"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="material-icons" data-toggle="tooltip" data-placement="top" title="Aktueller Benutzer">person</span>
                <span class="text-truncate">{{ simulationAccessLabel(simulation.allisa_user_id) }}</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <button type="button" class="dropdown-item" @click="$emit('switch-user', user.id)"
                        v-for="user in environmentUsers.filter(ele => ele.id !== simulation.allisa_user_id)">
                    {{ simulationAccessLabel(user.id) }}
                </button>
            </div>
        </template>
        <template v-else>
            <span class="material-icons" data-toggle="tooltip" data-placement="top" title="Aktueller Benutzer">person</span>
            {{ simulationAccessLabel(simulation.allisa_user_id) }}
        </template>
    </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    props: {
        allisaDemoUserId: String
    },
    computed: {
        ...mapGetters([
            'environments',
            'simulation',
            'definition'
        ]),
        userAccesses() {
            // Benutzer-Ids ermittelt die direkten Zugriff haben
            let accesses = {};
            let userAccesses = this.simulation.accesses.filter(ele => ele.recipient_type === 'User');

            for (let m = 0; m < userAccesses.length; m++) {
                if(accesses.hasOwnProperty(userAccesses[m].recipient_id)) {
                    accesses[userAccesses[m].recipient_id] = [...accesses[userAccesses[m].recipient_id], userAccesses[m].role_name]
                }
                else {
                    accesses[userAccesses[m].recipient_id] = [userAccesses[m].role_name];
                }

            }

            let environment = this.environments.find(ele => ele.id === this.simulation.environment_id);
            let groupAccesses = this.simulation.accesses.filter(ele => ele.recipient_type === 'Group');

            // Für jede hinzugefügte Grupep die Benutzer emittelt und das directUserAccesses-Array erweitern,
            // bzw. den role_names-Eintrag mit der Rolle der Gruppe erweitern.
            for (let i = 0; i < groupAccesses.length; i++) {
                let groupMembers = environment ? environment.blueprint.group_accesses.filter(ele => ele.group_id === groupAccesses[i].recipient_id) : [];

                for (let k = 0; k < groupMembers.length; k++) {
                    if (accesses.hasOwnProperty(groupMembers[k].user_id)) {
                        accesses[groupMembers[k].user_id] = [
                            ...accesses[groupMembers[k].user_id],
                            groupAccesses[i].role_name
                        ];
                    } else {
                        accesses[groupMembers[k].user_id] = [groupAccesses[i].role_name];
                    }
                }
            }

            return accesses;
        },
        environmentUsers() {
            let environment = this.environments.find(ele => ele.id === this.simulation.environment_id);
            let allisaDemoUser = {
                id: this.allisaDemoUserId,
                first_name: 'Demo',
                last_name: 'Benutzer'
            };

            if (environment) {
                return [
                    ...environment.blueprint.users,
                    allisaDemoUser
                ];
            }

            return [allisaDemoUser];
        }
    },
    methods: {
        simulationAccessLabel(userId) {
            let environment = this.environments.find(ele => ele.id === this.simulation.environment_id);
            let user = environment ? environment.blueprint.users.find(ele => ele.id === userId) : null;

            if (!user) {
                user = {
                    'first_name': 'Demo',
                    'last_name': 'Benutzer'
                };
            }

            return user.first_name + ' ' + user.last_name;
        },
    }
};
</script>
