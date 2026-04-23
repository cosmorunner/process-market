<template>
    <div class="input-group input-group-sm d-flex align-items-stretch">
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ groupLabels[newItem[0]] }}
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_1')">Gruppe 1</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_2')">Gruppe 2</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_3')">Gruppe 3</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_4')">Gruppe 4</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_5')">Gruppe 5</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_6')">Gruppe 6</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_7')">Gruppe 7</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_8')">Gruppe 8</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_9')">Gruppe 9</button>
                <button type="button" class="dropdown-item" @click="changeValue(0, 'group_10')">Gruppe 10</button>
            </div>
        </div>
        <div class="input-group-prepend">
            <DropdownSelector
                :syntax-include="syntaxLoaderInclude"
                :display-selected-value="true"
                :rounded-borders="false"
                @selected="onSelectDropdownLeft"
                ref="left-dropdown"
            />
        </div>
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ operatorLabels[newItem[2]] }}
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button type="button" v-for="(label, symbol) in operatorLabels"
                        class="dropdown-item" @click="changeValue(2, symbol)">
                    {{ label }}
                </button>
            </div>
        </div>
        <input type="text" class="form-control" aria-label="" v-if="!['is_empty_array', 'is_not_empty_array'].includes(newItem[2])"
               :value="newItem[3].startsWith('[[') && newItem[3].endsWith(']]') ? '' : newItem[3]"
               :readonly="newItem[3].startsWith('[[') && newItem[3].endsWith(']]')"
               placeholder="Leere Zeichenkette / NULL"
               @input="changeValue(3, $event.target.value)">
        <div class="input-group-append" v-if="!['is_empty_array', 'is_not_empty_array'].includes(newItem[2])">
            <DropdownSelector
                :syntax-include="syntaxLoaderInclude"
                :display-selected-value="true"
                :rounded-borders="false"
                @selected="onSelectDropdownRight"
                ref="right-dropdown"
            />
        </div>
        <div class="input-group-append">
            <button class="btn btn-sm rounded-right btn-outline-success" @click="addItem" type="button" :disabled="newItem[1].trim() === ''">
                <span class="material-icons">add</span>
            </button>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import OptionBadges from "../../utils/OptionBadges";
import DropdownSelector from "../../utils/DropdownSelector";

export default {
    components: {
        DropdownSelector,
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
            newItem: [
                'group_1',
                '',
                '=',
                ''
            ],
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
    methods: {
        ...utils,
        changeValue(index, value){
            let newItem = [...this.newItem];
            newItem[index] = value

            // Operator change. When comparing arrays, we empty last index because it is not necessary.
            if(index === 2 && ['is_empty_array', 'is_not_empty_array'].includes(value)) {
                newItem[3] = ''
            }

            this.newItem = newItem;
        },
        onSelectDropdownLeft(item) {
            let newItem = [...this.newItem];
            newItem[1] = item.value_with_label;

            this.newItem = newItem;
        },
        onSelectDropdownRight(item) {
            let newItem = [...this.newItem];
            newItem[3] = item.value_with_label;

            this.newItem = newItem;
        },
        addItem() {
            if (this.newItem[1] === '') {
                return;
            }

            this.newItem[1] = this.newItem[1].trim();
            this.newItem[3] = this.newItem[3].trim();

            this.$emit('add-condition', this.newItem);

            // Reset
            this.newItem = [
                'group_1',
                '',
                '=',
                ''
            ];

            // Dropdown-Einträge entfernen
            let leftDropdown = this.$refs['left-dropdown'];
            let rightDropdown = this.$refs['right-dropdown'];

            if (leftDropdown) {
                leftDropdown.clearLastSelectedValue();
            }
            if (rightDropdown) {
                rightDropdown.clearLastSelectedValue();
            }
        },
    }

};
</script>
