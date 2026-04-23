<template>
    <div class="bg-light p-1">
        <span class="d-block text-muted">{{ ruleIndex + 1 }}.</span>
        <table class="table table-sm table-borderless m-0">
            <tbody>
            <template v-for="(item, index) in items">
                <tr class="d-flex">
                    <td class="py-0 w-35 mb-0">
                        <span v-if="item[0].startsWith('[[') && item[0].endsWith(']]')">
                            <OptionBadges :value="item[0]" />
                        </span>
                        <span v-else :class="selectedColumnExists(item[0]) ? 'text-success' : 'text-danger'">
                            <span>{{ labelByColumn(item[0]) }}</span>
                        </span>
                    </td>
                    <td class="py-0 w-20 mb-0">{{ operatorLabels[item[1]] || '???' }}</td>
                    <td class="py-0 w-35 mb-0">
                        <span v-if="item[2] === ''"><i>Leere Zeichenkette</i></span>
                        <OptionBadges v-else :value="labelByColumn(item[2])" />
                    </td>
                    <td class="py-0 w-10 mb-0">
                        <button class="btn btn-sm btn-light float-right" @click="$emit('delete-item', ruleIndex, index)" v-if="editable">
                            <span class="material-icons text-danger">close</span>
                        </button>
                    </td>
                </tr>
                <tr v-if="items.length > 1 && (index !== items.length - 1)">
                    <td class="text-muted p-0 pl-2">
                        <small>{{ connectingOperator }}</small>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>

import OptionBadges from "../../../utils/OptionBadges";

export default {
    components: {OptionBadges},
    props: {
        allColumns: Array,
        usedColumnAliases: Array | Object,
        connectingOperator: String,
        ruleIndex: Number,
        item: Array,
        editable: {
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
                '>': 'Größer als',
                'ilike': 'Zeichenkette beinhaltet',
                'not_ilike': 'Zeichenkette beinhaltet nicht',
                'json_array_contains': 'JSON-Array beinhaltet',
                'json_array_not_contains': 'JSON-Array beinhaltet nicht'
            }
        };
    },
    computed: {
        items() {
            // Falls genestete Items vorhanden sind.
            if (Array.isArray(this.item[0])) {
                return this.item;
            } else {
                return [this.item];
            }
        }
    },
    methods: {
        labelByColumn(column){
            let item = this.allColumns.find(ele => ele.column === column)

            if(item) {
                return item.label
            }

            return column
        },
        selectedColumnExists(column){
            return this.usedColumnAliases.find(ele => ele.column === column)
        }
    }
};
</script>
