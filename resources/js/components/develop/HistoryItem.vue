<template>
    <div>
        <div class="card-header d-flex align-items-center justify-content-between px-2 py-1">
            <div v-if="simulation.running" class="btn p-0 pr-1 mouse-pointer" data-toggle="collapse"
                 :data-target="'#collapse-history-' + id" @click="toggleContent">
                     <span class="material-icons mr-2">
                      {{ isCollapsed ? 'keyboard_arrow_up' : 'keyboard_arrow_down' }}
                    </span>
                <span>{{ step === 1 ? 'Start' : step }} - {{ actionType.name }}</span>
            </div>
            <button v-if="action.can_revert"
                    :class="'align-items-center justify-content-between btn btn-sm ' + (simulation.undoing !== action.id ? 'btn-success' : 'btn-warning')"
                    type="button" @click="revertTo(action.id)" :disabled="simulation.undoing !== null">
                <span v-if="simulation.undoing !== action.id" class="material-icons">undo</span>
                <span v-if="simulation.undoing === action.id" class="material-icons">more_horiz</span>
            </button>
        </div>
        <div :id="'collapse-history-' + id" class="card-body p-2 border-bottom transition-all duration-300 collapse">
            <template v-for="status in action.status_data" v-if="Object.keys(action.status_data).length">
                <div class="row no-gutters">
                    <div class="col">
                        <span class="text-muted"> {{ status.name }}</span>
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col-5">
                        <div class="row no-gutters">
                            <div class="col-1 py-1">
                                <span class="material-icons" v-if="previousState(status.status_type_reference).image"
                                      :style="'color:' + previousState(status.status_type_reference).color">{{
                                        previousState(status.status_type_reference).image
                                    }}</span>
                            </div>
                            <div class="col-11 p-1">
                                <p class="pl-1 m-0">
                                    <span>{{ previousStateTextValue(status.status_type_reference) }}</span>
                                </p>
                                <p class="pl-1 m-0 text-muted">{{ previousStateValue(status.status_type_reference) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <span class="material-icons">chevron_right</span>
                    </div>
                    <div class="col-6">
                        <div class="row no-gutters">
                            <div class="col-1 py-1">
                                <template v-if="state(status.value, statusType(status.status_type_reference)).image">
                                    <span class="material-icons"
                                          :style="'color:' + state(status.value, statusType(status.status_type_reference)).color">
                                        {{ state(status.value, statusType(status.status_type_reference)).image }}
                                    </span>
                                </template>
                            </div>
                            <div class="col-11 p-1">
                                <p class="pl-1 m-0">
                                    <span>{{ status.text_value }}</span>
                                </p>
                                <p class="pl-1 m-0 text-muted">{{ status.value }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <span class="text-muted" v-if="!Object.keys(action.status_data).length">Keine Situations-Änderung.</span>
            <hr v-if="Object.keys(action.action_data).length"/>
            <div class="row no-gutters" v-if="Object.keys(action.action_data).length">
                <div class="col">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th class="px-2 py-1 border-0">
                                <small class="text-muted">Name</small>
                            </th>
                            <th class="px-2 py-1 border-0">
                                <small class="text-muted">Wert</small>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(value, name) in action.action_data">
                            <td class="px-2 py-1 text-break">
                                <span>{{ name }}</span>
                            </td>
                            <td class="px-2 py-1 text-break">
                                <span v-if="typeof value === 'string'">
                                    <span v-if="value === ''"><i>Leere Zeichenkette</i></span>
                                    <span v-else-if="value === null"><i>Leer</i></span>
                                    <span v-else>{{ value.substring(0, 50) }}</span>
                                </span>
                                <ul v-else-if="Array.isArray(value)" class="list-group list-group-flush">
                                    <span v-if="!value.length"><i>Leer</i></span>
                                    <li class="list-group-item px-0 py-1" v-for="(item, index) in value"
                                        v-if="value.length && typeof item !== 'undefined'">
                                        <span class="text-muted">{{ index + 1 }}: </span>
                                        <span v-if="item === ''"><i>Leere Zeichenkette</i></span>
                                        <span v-else-if="item === null"><i>Leer (Null)</i></span>
                                        <span v-else>{{ item }}</span>
                                    </li>
                                </ul>
                                <ul v-else-if="typeof value === 'object' && value !== null" class="list-group list-group-flush">
                                    <span v-if="!Object.keys(value).length"><i>Leer</i></span>
                                    <li v-else class="list-group-item px-0 py-1" v-for="(item, key) in value">
                                        <span class="text-muted">{{ key }}: </span>
                                        <span v-if="item === ''"><i>Leere Zeichenkette</i></span>
                                        <span v-else-if="item === null"><i>Leer (Null)</i></span>
                                        <span v-else-if="typeof item === 'object' || Array.isArray(item)">{{
                                                JSON.stringify(item)
                                            }}</span>
                                        <span v-else>{{ item }}</span>
                                    </li>
                                </ul>
                                <span v-else>{{
                                        (value || '').length > 60 ? (value || '').substr(0, 60) + '...' : value
                                    }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <span class="text-muted" v-else>Keine Aktions-Daten.</span>
            <div v-if="action.processor_executions.length">
                <hr/>
                <small class="text-muted d-block mb-1">Prozessor-Ausführungen</small>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item py-1 px-0" v-for="(amount, identifer) in groupedProcessors">
                        <span class="material-icons">{{ processorIcons(identifer) }}</span>
                        <span>{{ processorNames(identifer) }}</span>
                        <span class="badge badge-light" v-if="amount > 1">{{ amount }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import OptionBadges from "../utils/OptionBadges";

export default {
    components: {OptionBadges},
    props: {
        action: Object,
        id: String,
        step: Number
    },
    data() {
        return {
            isCollapsed: false,
        };
    },
    computed: {
        ...mapGetters([
            'action_types',
            'status_types',
            'simulation',
            'active_action_type_ids',
            'inaccessible_action_type_ids',
            'active_state_ids'
        ]),
        actionType() {
            return this.action_types.find(ele => ele.id === this.action.action_type_id);
        },
        groupedProcessors() {
            let grouped = {};

            for (let execution of this.action.processor_executions) {
                let identifier = execution.processor.identifier;

                if (grouped.hasOwnProperty(identifier)) {
                    grouped[identifier]++;
                }
                else {
                    grouped[identifier] = 1;
                }
            }

            return grouped;
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        state(value, statusType) {
            let floatVal = parseFloat(value);

            if (statusType) {
                for (let i = 0; i < statusType.states.length; i++) {
                    if (parseFloat(statusType.states[i].min) <= floatVal && parseFloat(statusType.states[i].max) >= floatVal) {
                        return statusType.states[i];
                    }
                }
            }

            return {
                color: '#013370',
                description: 'Nicht definiert',
                image: 'help_outline',
                min: '',
                max: '',
                status_type_reference: this.statusType.reference,
                visible: 1
            };
        },
        statusType(statusTypeReference) {
            return this.status_types.find(ele => ele.reference === statusTypeReference);
        },
        previousState(statusTypeReference) {
            let previousStatus = this.action.previous_status_data.find(ele => ele.status_type_reference === statusTypeReference);

            if (!previousStatus) {
                return this.state(0.000, undefined);
            }

            return this.state(previousStatus.value, this.statusType(statusTypeReference));
        },
        previousStateValue(statusTypeReference) {
            let previousStatus = this.action.previous_status_data.find(ele => ele.status_type_reference === statusTypeReference);

            if (!previousStatus) {
                return '';
            }

            return previousStatus.value;
        },
        previousStateTextValue(statusTypeReference) {
            let previousStatus = this.action.previous_status_data.find(ele => ele.status_type_reference === statusTypeReference);

            if (!previousStatus) {
                return '';
            }

            return previousStatus.text_value;
        },
        toggleContent() {
            this.isCollapsed = !this.isCollapsed;
        },
    }
};
</script>
