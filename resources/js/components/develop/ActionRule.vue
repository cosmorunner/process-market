<template v-if="actionRule && statusType">
    <tr class="d-flex">
        <td :class="editable ? 'col-11 border-0 px-2 py-1 mouse-pointer' : 'col-12 border-0 px-2 py-1'" @click="editable && openActionRuleModal($event)">
            <template>
                <div v-if="hasStringDescription(actionRule.operator)">
                    {{ statusType.name }} <span class="text-actionrule">{{ operator(actionRule.operator) }}</span>
                    <span class="mt-1 badge badge-pill badge-light font-weight-normal border" v-if="actionRule.values.length">
                        {{ actionRule.values[0] }}
                    </span>
                    <span class="mt-1 badge badge-pill badge-light font-weight-normal border" v-if="actionRule.state_ids.length">
                        <span class="material-icons" :style="'color:' + state(actionRule.state_ids[0]).color">{{ state(actionRule.state_ids[0]).image }}</span>
                        <span>{{ state(actionRule.state_ids[0]).description }}</span>
                    </span>
                </div>
                <div v-else>
                    {{ statusType.name }} <span class="text-actionrule">{{ operator(actionRule.operator) }}</span>
                    <template v-if="actionRule.values.length">
                        <ul class="list-group" v-if="actionRule.values.length > 1">
                            <li v-for="value in actionRule.values" class="list-group-item border-0 p-1 bg-transparent">
                            <span class="mt-1 badge badge-pill badge-light font-weight-normal border">
                                <span class="material-icons"
                                      :style="'color:' + state(value).color">{{ state(value).image }}</span>
                                {{ state(value).description }}
                            </span>
                            </li>
                        </ul>
                        <span class="mt-1 badge badge-pill badge-light font-weight-normal border" v-else>
                                <span class="material-icons"
                                      :style="'color:' + state(actionRule.values[0]).color">{{ state(actionRule.values[0]).image }}</span>
                                {{ state(actionRule.values[0]).description }}
                            </span>
                    </template>
                    <template v-if="actionRule.state_ids.length">
                        <ul class="list-group" v-if="actionRule.state_ids.length > 1">
                            <li v-for="state_id in actionRule.state_ids" class="list-group-item border-0 p-1 bg-transparent">
                            <span class="mt-1 badge badge-pill badge-light font-weight-normal border">
                                <span class="material-icons"
                                      :style="'color:' + state(state_id).color">{{ state(state_id).image }}</span>
                                {{ state(state_id).description }}
                            </span>
                            </li>
                        </ul>
                        <span class="mt-1 badge badge-pill badge-light font-weight-normal border" v-else>
                            <span class="material-icons"
                                  :style="'color:' + state(actionRule.state_ids[0]).color">{{ state(actionRule.state_ids[0]).image }}</span>
                            {{ state(actionRule.state_ids[0]).description }}
                        </span>
                    </template>
                </div>
            </template>
        </td>
        <td class="col-1 border-0 px-2 py-1" v-if="!hideDeleteButton && editable && ui.editable">
            <button class="btn btn-sm float-right btn-light"
                    @click="onDeleteActionRule(actionRule.action_type_id, statusType.id)">
                <span class="material-icons text-danger">delete</span>
            </button>
        </td>
    </tr>
</template>
<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';

export default {
    props: {
        actionRule: Object,
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
            'simulation',
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        state(value) {
            if (value.length === 36) {
                return this.statusType.states.find(ele => ele.id === value);
            }
            else {
                let floatVal = parseFloat(value);

                for (let i = 0; i < this.statusType.states.length; i++) {
                    if (parseFloat(this.statusType.states[i].min) <= floatVal && parseFloat(this.statusType.states[i].max) >= floatVal) {
                        return this.statusType.states[i];
                    }
                }
            }

            return {
                'id': '',
                'status_type_id': '',
                'description': `Nicht definiert (${value})`,
                'image': 'help_outline',
                'min': '',
                'max': '',
                'visible': 1,
                'color': '#ffffff'
            };
        },
        operator(operatorString) {
            // Wenn der Operator IN_ARRAY ist und values oder state_ids nur einen Wert hat, soll "ist gleich"
            // dort stehen.
            if (operatorString === 'IN_ARRAY' && (this.actionRule.values.length === 1 || this.actionRule.state_ids.length === 1)) {
                operatorString = 'EQUALS';
            }

            let operators = {
                'EQUALS': 'ist gleich',
                'IN_ARRAY': 'ist entweder oder',
                'NOT_IN_ARRAY': 'ist nicht gleich',
                'LOWER': 'ist kleiner als',
                'LOWER_OR_EQUAL': 'ist kleiner oder gleich',
                'GREATER_OR_EQUAL': 'ist größer oder gleich',
                'GREATER': 'ist größer als',
                'IN_BETWEEN': 'ist zwischen',
            };

            return operators.hasOwnProperty(operatorString) ? operators[operatorString] : 'unbekannt';
        },
        hasStringDescription: function (operator) {
            return [
                'LOWER',
                'LOWER_OR_EQUAL',
                'GREATER_OR_EQUAL',
                'GREATER',
            ].includes(operator);
        },
        openActionRuleModal() {
            this.openModal({
                componentName: 'ActionRuleModal',
                data: {
                    statusTypeId: this.actionRule.status_type_id,
                    actionTypeId: this.actionRule.action_type_id,
                    actionRuleId: this.actionRule.id
                }
            });
        }
    }
};
</script>

<style scoped>

td.mouse-pointer:hover {
    background: #f8f9fa;
}

</style>
