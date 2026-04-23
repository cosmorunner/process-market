<template>
    <div class="input-group input-group-sm mt-2 d-flex align-items-stretch">
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ groupLabels[newItem.group] }}
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button type="button" class="dropdown-item" @click="changeValue" data-name="group"
                        data-value="group_1">Gruppe 1
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-name="group"
                        data-value="group_2">Gruppe 2
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-name="group"
                        data-value="group_3">Gruppe 3
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-name="group"
                        data-value="group_4">Gruppe 4
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-name="group"
                        data-value="group_5">Gruppe 5
                </button>
            </div>
        </div>
        <!-- Statustyp wählen -->
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span v-if="selectedStatusType">{{ newItem.namespace }} - {{ selectedStatusType.name }}</span>
                <span v-else>#</span>
            </button>
            <div class="dropdown-menu dropdown-menu scrollable-dropdown">
                <button type="button" class="dropdown-item" v-for="statusType in latestStatusTypes"
                        @click="changeStatusType(statusType)">
                    {{ statusType.namespace.split('@')[0] }} - {{ statusType.name }}
                </button>
            </div>
        </div>
        <!-- Operator -->
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ operatorLabels[newItem.operator] }}
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button type="button" v-for="(label, symbol) in operatorLabels"
                        class="dropdown-item" @click="changeValue" data-name="operator" :data-value="symbol">
                    {{ label }}
                </button>
            </div>
        </div>
        <select class="custom-select" v-model="newItem.state_id" @change="changeState">
            <option value="">...</option>
            <template v-if="selectedStatusType">
                <option v-for="state in selectedStatusType.states" :value="state.id">{{ state.description }}</option>
            </template>
        </select>
        <div class="input-group-append">
            <button class="btn btn-sm rounded-right btn-outline-success" @click="addItem" type="button">
                <span class="material-icons">add</span>
            </button>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import OptionBadges from "../../utils/OptionBadges";
import Template from "../../../../plugins/Allisa/ActionTypeComponent/Form/v1_0_0/configuration/Template";

export default {
    components: {
        Template,
        OptionBadges
    },
    props: {
        conditions: Array,
        syntaxLoaderInclude: {
            type: Array,
            default: () => [
                'action.outputs',
                'process.outputs',
                'process.meta',
                'process.status',
                'auth',
                'process.identity',
                'date'
            ]
        }
    },
    data() {
        return {
            newItem: {
                group: 'group_1',
                type: 'status',
                namespace: '',
                status_type_id: '',
                status_type_name: '',
                operator: '=',
                state_id: '',
                state_description: ''
            },
            selectedStatusType: null,
            selectedState: null,
            operatorLabels: {
                '=': 'Gleich',
                '!=': 'Nicht gleich',
            },
            groupLabels: {
                'group_1': 'Gruppe 1',
                'group_2': 'Gruppe 2',
                'group_3': 'Gruppe 3',
                'group_4': 'Gruppe 4',
                'group_5': 'Gruppe 5',
            }
        };
    },
    computed: {
        ...mapGetters([
            'graphs_status_types',
            'relation_types'
        ]),
        latestStatusTypes() {
            return this.graphs_status_types.filter(ele => !ele.smart && ele.states.length && ele.namespace.split('@')[1] === 'latest').sort((a, b) => a.namespace > b.namespace ? 1 : -1);
        }
    },
    methods: {
        ...utils,
        changeValue(e) {
            let name = e.target.dataset.name;

            this.newItem = {
                ...this.newItem,
                [name]: e.target.dataset.value || e.target.value
            };
        },
        changeStatusType(statusType) {
            this.selectedStatusType = statusType;

            let stateId = '';
            let stateDescription = '';

            if (statusType.states) {
                this.selectedState = statusType.states[0];
                stateId = this.selectedState.id;
                stateDescription = this.selectedState.description;
            }

            this.newItem = {
                ...this.newItem,
                namespace: statusType.namespace.split('@')[0],
                status_type_name: statusType.name,
                status_type_id: statusType.id,
                operator: this.newItem.operator,
                state_id: stateId,
                state_description: stateDescription
            };
        },
        changeState(e) {
            let stateId = e.target.value

            this.selectedState = this.selectedStatusType.states.find(ele => ele.id === stateId);

            this.newItem = {
                ...this.newItem,
                state_id: this.selectedState.id,
                state_description: this.selectedState.description
            };
        },
        addItem() {
            if (this.newItem.status_type_id === '' || this.newItem.state_id === '') {
                return;
            }

            this.$emit('conditions-change', [
                ...(this.conditions || []),
                this.newItem
            ]);

            this.selectedStatusType = null;
            this.selectedState = null;

            // Reset
            this.newItem = {
                group: 'group_1',
                type: 'status',
                namespace: '',
                status_type_id: '',
                status_type_name: '',
                operator: '=',
                state_id: '',
                value: '',
                state_description: ''
            };
        },
    }

};
</script>
