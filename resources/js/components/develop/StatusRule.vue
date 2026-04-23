<template v-if="statusRule && statusType">
    <tr class="d-flex">
        <td :class="editable ? 'col-11 border-0 px-2 py-1 mouse-pointer' : 'col-12 border-0 px-2 py-1'" @click="editable && openStatusRuleModal($event)">
            <template>
                <!-- SET und fixer Wert (Manual Value) -->
                <div
                    v-if="statusRule.operator === 'SET' && !statusRule.output && !statusRule.state && statusRule.values.length">
                    <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span>
                    <span>{{ statusType.name }}</span>
                    <span class="text-statusrule">{{ operatorPartTwo(statusRule.operator) }}</span>
                    <span class="mt-1 badge badge-pill badge-light">
                        <span class="material-icons"
                              :style="'color:' + stateByValue(statusRule.values[0]).color">{{
                                stateByValue(statusRule.values[0]).image
                            }}</span>
                        <OptionBadges :value="stateByValue(statusRule.values[0]).description"/>
                    </span>
                </div>
                <!-- SET und fixer Wert (State)  -->
                <div
                    v-if="statusRule.operator === 'SET' && statusRule.state && !statusRule.output && !statusRule.values.length">
                    <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span>
                    <span>{{ statusType.name }}</span>
                    <span class="text-statusrule">{{ operatorPartTwo(statusRule.operator) }}</span>
                    <span class="mt-1 badge badge-pill badge-light font-weight-normal border">
                        <span class="material-icons"
                              :style="'color:' + stateById(statusRule.state).color">{{
                                stateById(statusRule.state).image
                            }}</span>
                        <OptionBadges :value="stateById(statusRule.state).description"/>
                    </span>
                </div>
                <!-- SET und dynamischer Wert -->
                <div v-if="statusRule.operator === 'SET' && statusRule.output && !statusRule.state">
                    <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span>
                    <span>{{ statusType.name }}</span>
                    <span class="text-statusrule">{{ operatorPartTwo(statusRule.operator) + ' den Wert von' }}</span>
                    <OptionBadges :value="statusRule.output" :disable-tooltip="true"/>
                </div>
                <!-- ADD/SUB und fixer Wert -->
                <div
                    v-if="(statusRule.operator === 'ADD' || statusRule.operator === 'SUB') && !statusRule.output && !statusRule.conditions.length">
                    <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span> <span
                    class="mt-1 badge badge-pill badge-light">{{ statusRule.values[0] }}</span> <span
                    class="text-statusrule">{{ operatorPartTwo(statusRule.operator) }}</span>
                    <span>{{ statusType.name }}</span>
                </div>
                <!-- ADD/SUB und dynamischer Wert -->
                <div
                    v-if="(statusRule.operator === 'ADD' || statusRule.operator === 'SUB') && statusRule.output">
                    <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span> den Wert von
                    <OptionBadges :value="statusRule.output"/>
                    <span class="text-statusrule">{{ operatorPartTwo(statusRule.operator) }}</span> {{
                        statusType.name
                    }}
                </div>
                <!-- Conditions -->
                <div v-if="Array.isArray(statusRule.conditions)
                 && !statusRule.output && statusRule.conditions.length
                 && !statusRule.values.length">
                    <template v-for="(conditions, stateIdOrValue) in conditionsByStateIdOrValue">
                        <span v-if="statusRule.operator === 'SET'" class="d-inline-block mb-1">
                            <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span>
                            {{ statusType.name }} <span
                            class="text-statusrule">{{ operatorPartTwo(statusRule.operator) }}</span>
                            <span class="mt-1 badge badge-pill badge-light font-weight-normal border">
                                <span v-if="stateIdOrValue.length === 36">
                                    <span class="material-icons" :style="'color:' + stateById(stateIdOrValue).color">{{ stateById(stateIdOrValue).image }}</span>
                                    <span>{{ stateById(stateIdOrValue).description }}</span>
                                </span>
                                <span v-else>
                                    <span class="material-icons" :style="'color:' + stateByValue(stateIdOrValue).color">{{ stateByValue(stateIdOrValue).image }}</span>
                                    <span>{{ stateByValue(stateIdOrValue).description }}</span>
                                </span>
                            </span>
                            <span>wenn:</span>
                        </span>
                        <span v-if="(statusRule.operator === 'ADD' || statusRule.operator === 'SUB')" class="d-inline-block mb-1">
                            <span class="text-statusrule">{{ operatorPartOne(statusRule.operator) }}</span>
                            <OptionBadges :value="stateIdOrValue.length === 36 ? stateById(stateIdOrValue).min : stateIdOrValue" :disable-tooltip="true"/>
                            <span class="text-statusrule">{{ operatorPartTwo(statusRule.operator) }}</span>
                            {{ statusType.name }}
                            <span>wenn:</span>
                        </span>
                        <div class="mb-2">
                            <ConditionsTable :edit-mode="false" :editable="false" :conditions="castedConditions(conditions)" />
                        </div>
                    </template>
                </div>
            </template>
        </td>
        <td class="col-1 border-0 px-2 py-1" v-if="!hideDeleteButton && editable && ui.editable">
            <button class="btn btn-sm float-right btn-light"
                    @click="onDeleteStatusRule(statusRule.action_type_id, statusType.id)">
                <span class="material-icons text-danger">delete</span>
            </button>
        </td>
    </tr>
</template>
<script>

import OptionBadges from "../utils/OptionBadges";
import utils from "../../develop-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";
import ConditionsTable from "../config/partials/ConditionsTable.vue";

export default {
    components: {
        ConditionsTable,
        OptionBadges},
    props: {
        statusRule: Object,
        statusType: Object,
        hideDeleteButton: {
            default: false,
            type: Boolean
        },
        editable: {
            default: false,
            type: Boolean
        }
    },
    computed: {
        ...mapGetters([
            'ui',
            'simulation'
        ]),
        conditionsByStateIdOrValue() {
            let grouped = {};

            for (let i = 0; i < this.statusRule.conditions.length; i++) {
                let stateId = this.statusRule.conditions[i][0];

                if (grouped.hasOwnProperty(stateId)) {
                    grouped[stateId].push(this.statusRule.conditions[i]);
                } else {
                    grouped[stateId] = [this.statusRule.conditions[i]];
                }
            }

            // Nach Gruppen-Nummer sortieren
            grouped = Object.keys(grouped).sort().reduce((obj, key) => ({
                ...obj,
                [key]: grouped[key]
            }), {});

            return grouped;
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        stateByValue(value) {
            let floatVal = parseFloat(value);

            for (let i = 0; i < this.statusType.states.length; i++) {
                if (parseFloat(this.statusType.states[i].min) <= floatVal && parseFloat(this.statusType.states[i].max) >= floatVal) {
                    return this.statusType.states[i];
                }
            }

            return this.unknownState(value);
        },
        stateById(id) {
            let state = this.statusType.states.find(ele => ele.id === id);

            if (state) {
                return state;
            }

            return this.unknownState();
        },
        unknownState(value) {
            if (!value) {
                value = '?';
            }

            return {
                'id': '',
                'status_type_id': '',
                'description': 'Nicht definiert ' + '(' + value + ')',
                'image': 'help_outline',
                'min': '',
                'max': '',
                'visible': 1,
                'color': '#212529'
            };
        },
        operatorPartOne(operatorString) {
            let operators = {
                'SET': 'Setzt',
                'ADD': 'Addiert',
                'SUB': 'Subtrahiert',
            };

            return operators.hasOwnProperty(operatorString) ? operators[operatorString] : 'Unbekannt';
        },
        operatorPartTwo(operatorString) {
            let operators = {
                'SET': 'auf',
                'ADD': 'zu',
                'SUB': 'von',
            };

            return operators.hasOwnProperty(operatorString) ? operators[operatorString] : 'Unbekannt';
        },
        textForComparison(comparison) {
            switch (comparison) {
                case '=':
                case '==':
                case '===':
                    return 'ist gleich';
                case '!=':
                case '!==':
                    return 'ist nicht gleich';
                case '<=':
                    return 'ist kleiner oder gleich';
                case '>=':
                    return 'ist größer oder gleich';
                case '>':
                    return 'ist größer als';
                case '<':
                    return 'ist kleiner als';
            }
        },
        openStatusRuleModal() {
            this.openModal({
                componentName: 'StatusRuleModal',
                data: {
                    statusTypeId: this.statusRule.status_type_id,
                    actionTypeId: this.statusRule.action_type_id,
                    statusRuleId: this.statusRule.id
                }
            });
        },
        castedConditions(conditions) {
            // Statusrules without first element to be displayed in ConditionsTable component
            return conditions.map(function (ele) {
                let condition = [...ele];
                condition.shift();

                return condition;
            });
        },
    },
};
</script>

<style scoped>

td.mouse-pointer:hover {
    background: #f8f9fa;
}

</style>
