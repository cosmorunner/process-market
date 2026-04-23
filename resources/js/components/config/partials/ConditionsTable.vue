<template>
    <div>
        <template v-for="(conditions, groupKey) in conditionsByGroup">
            <div class="card">
                <div class="card-header px-1 py-0">
                    <small class="text-muted">{{ groupLabels[groupKey] }}</small>
                </div>
                <div class="card-body px-1 py-0">
                    <table class="table table-sm table-borderless m-0 p-1">
                        <tbody>
                        <template v-for="(condition, index) in conditions">
                            <tr class="d-flex">
                                <td :class="'pl-1 py-1 mb-0 ' + (editMode && editable ? 'col-4' : 'col-6')">
                                    <OptionBadges :value="condition[1]" />
                                </td>
                                <td class="py-1 col-3 mb-0">{{ operatorLabels[condition[2]] || '???' }}</td>
                                <td class="py-1 col-3 mb-0">
                                    <OptionBadges :value="condition[3]" v-if="condition[3]" />
                                    <span v-else-if="!['is_empty_array', 'is_not_empty_array'].includes(condition[2])"><i>Leere Zeichenkette / NULL</i></span>
                                </td>
                                <td class="pr-1 py-1 col-2 mb-0" v-if="editMode && editable">
                                    <button class="btn btn-sm btn-light float-right" type="button"
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
            </div>
            <span v-if="!lastIndex(Object.keys(conditionsByGroup), groupKey)"
                  class="text-muted pl-1 pt-2 pb-1 d-block">
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
        },
        editable: {
            default: true,
            type: Boolean
        },
    },
    data() {
        return {
            operatorLabels: {
                '=': 'Gleich',
                '!=': 'Nicht gleich',
                '<': 'Kleiner als',
                '<=': 'Kleiner oder gleich',
                '>=': 'Größer oder gleich',
                '>': 'Größer als',
                'is_empty_array': 'Ist leer (JSON-Array/Objekt)',
                'is_not_empty_array': 'Ist nicht leer (JSON-Array/Objekt)'
            },
            groupLabels: {
                'group_1': 'Gruppe 1',
                'group_2': 'Gruppe 2',
                'group_3': 'Gruppe 3',
                'group_4': 'Gruppe 4',
                'group_5': 'Gruppe 5',
                'group_6': 'Gruppe 6',
                'group_7': 'Gruppe 7',
                'group_8': 'Gruppe 8',
                'group_9': 'Gruppe 9',
                'group_10': 'Gruppe 10',
            }
        };
    },
    computed: {
        conditionsByGroup() {
            let grouped = {};

            for (let i = 0; i < this.conditions.length; i++) {
                let groupName = this.conditions[i][0];

                if (grouped.hasOwnProperty(groupName)) {
                    grouped[groupName].push(this.conditions[i]);
                } else {
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
    },
    methods: {
        lastIndex(items, key) {
            return items.indexOf(key) === items.length - 1;
        }
    }
};
</script>
