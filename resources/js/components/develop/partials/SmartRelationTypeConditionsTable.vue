<template>
    <div>
        <template v-for="(conditions, groupKey) in conditionsByGroup()">
            <div class="p-1">
                <span class="p-1 text-primary" v-if="showGroupLabels">
                    <small>{{ groupLabels[groupKey] }}</small>
                </span>
                <table class="table table-sm table-borderless m-0">
                    <tbody>
                    <template v-for="(condition, index) in conditions">
                        <tr class="d-flex">
                            <td class="py-0 col-4 mb-0">
                                <span>{{ condition.namespace }} - {{
                                        condition.status_type_name
                                    }}</span>
                            </td>
                            <td class="py-0 col-2 mb-0">{{ operatorLabels[condition.operator] || '???' }}</td>
                            <td class="py-0 col-4 mb-0">
                                <span>{{ condition.state_description }}</span>
                            </td>
                            <td class="py-0 col-2 mb-0" v-if="editMode">
                                <button class="btn btn-sm btn-light float-right"
                                        @click="$emit('delete-item', condition)">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="index !== conditions.length - 1">
                            <td class="text-muted p-0 pl-1">
                                <small>und</small>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <span v-if="!lastIndex(Object.keys(conditionsByGroup()), groupKey)"
                  class="text-muted d-block pl-3 border-top border-bottom">
                <small>oder</small>
            </span>
        </template>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";

export default {
    components: {OptionBadges},
    props: {
        conditions: Array,
        editMode: {
            default: true,
            type: Boolean
        },
        showGroupLabels: {
            default: true,
            type: Boolean
        }
    },
    data() {
        return {
            operatorLabels: {
                '=': 'Gleich',
                '!=': 'Nicht gleich',
                '<': 'Kleiner als',
                '<=': 'Kleiner oder gleich',
                '>=': 'Größer oder gleich',
                '>': 'Größer als'
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
    methods: {
        conditionsByGroup() {
            let grouped = {};

            for (let i = 0; i < this.conditions.length; i++) {
                let groupName = this.conditions[i].group;

                if (grouped.hasOwnProperty(groupName)) {
                    grouped[groupName].push(this.conditions[i]);
                }
                else {
                    grouped[groupName] = [this.conditions[i]];
                }
            }

            // Nach Gruppen-Nummer sortieren
            grouped = Object.keys(grouped).sort().reduce((obj, key) => ({
                ...obj,
                [key]: grouped[key]
            }), {});

            return grouped;
        },
        lastIndex(items, key) {
            return items.indexOf(key) === items.length - 1;
        }
    }
};
</script>
