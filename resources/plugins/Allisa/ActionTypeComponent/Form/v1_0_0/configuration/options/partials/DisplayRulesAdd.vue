<template>
    <div class="input-group input-group-sm mb-3 d-flex align-items-stretch">
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                {{ groupLabels[newItem[0]] }}
            </button>
            <div class="dropdown-menu">
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_1">
                    Gruppe 1
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_2">
                    Gruppe 2
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_3">
                    Gruppe 3
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_4">
                    Gruppe 4
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_5">
                    Gruppe 5
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_6">
                    Gruppe 6
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_7">
                    Gruppe 7
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_8">
                    Gruppe 8
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_9">
                    Gruppe 9
                </button>
                <button type="button" class="dropdown-item" @click="changeValue" data-index="0" data-value="group_10">
                    Gruppe 10
                </button>
            </div>
        </div>
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <template v-if="newItem[1] === 'auth|process_role_npms'">
                    Prozess-Rollen des Benutzers
                </template>
                <template v-else>
                    {{ newItem[1].split('|')[1] || 'Wählen...' }}
                </template>
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button type="button" class="dropdown-item" v-for="inputName in inputNames" @click="changeLeftValue"
                        data-index="1" :data-value="'field|' + inputName">
                    {{ inputName }}
                </button>
                <div class="dropdown-divider"></div>
                <button type="button" class="dropdown-item" @click="changeLeftValue" data-index="1"
                        :data-value="'auth|process_role_npms'">Prozess-Rollen des Benutzers
                </button>
            </div>
        </div>
        <div class="input-group-prepend">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                {{ operatorLabels[newItem[2]] }}
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <button type="button" v-for="(label, symbol) in selectedOperatorLabels" class="dropdown-item"
                        @click="changeValue" data-index="2" :data-value="symbol">
                    {{ label }}
                </button>
            </div>
        </div>
        <input type="text" class="form-control" aria-label="" :value="inputValue" :placeholder="isRuleForRole ? '' : 'Leere Zeichenkette/NULL'"
               :readonly="inputReadonly" @input="changeValue" data-index="3">
        <div class="input-group-append">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <span class="material-icons" v-if="isRuleForRole && !newItem[3]">person</span>
                <span v-else>{{ buttonValue }}</span>
            </button>
            <div class="dropdown-menu scrollable-dropdown">
                <template v-if="!isRuleForRole">
                    <button type="button" class="dropdown-item" @click="changeValue" data-index="3" :data-value="''">
                        Eigener Wert
                    </button>
                    <button type="button" class="dropdown-item" @click="changeValue" data-index="3" :data-value="'js|true'">
                        TRUE
                    </button>
                    <button type="button" class="dropdown-item" @click="changeValue" data-index="3"
                            :data-value="'js|false'">
                        FALSE
                    </button>
                    <button type="button" class="dropdown-item" @click="changeValue" data-index="3"
                            :data-value="'js|empty_array_object'">
                        Leeres JSON Array/Objekt
                    </button>
                    <div class="dropdown-divider"></div>
                    <button type="button" class="dropdown-item" v-for="inputName in inputNames" @click="changeValue"
                            data-index="3" :data-value="'field|' + inputName">
                        {{ inputName }}
                    </button>
                </template>
                <template v-if="isRuleForRole">
                    <div class="dropdown-divider" v-if="roles.length"></div>
                    <button type="button" class="dropdown-item" v-for="role in roles" @click="changeValue"
                            data-index="3" :data-value="'role|' + role.id + '[' + role.name + ']'">
                        {{ role.name }}
                    </button>
                </template>
            </div>
        </div>
        <div class="input-group-append">
            <button class="btn btn-sm rounded-right btn-outline-success" @click="addItem" :disabled="isRuleForRole && !newItem[3]">
                <span class="material-icons">add</span>
            </button>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        inputNames: Array,
        roles: {
            default: function () {
                return [];
            }
        },
        part: String,
        operatorLabels: Object,
        groupLabels: Object,
        displayRules: Array
    },
    data() {
        return {
            newItem: [
                'group_1',
                '',
                '=',
                ''
            ]
        };
    },
    computed: {
        inputValue() {
            if (this.newItem[3].startsWith('field|') || this.newItem[3].startsWith('js|') || this.newItem[3].startsWith('role|')) {
                return '';
            }

            return this.newItem[3];
        },
        inputReadonly() {
            return this.newItem[3].startsWith('field|') || this.newItem[3].startsWith('js|') || this.newItem[3].startsWith('role|') || this.isRuleForRole;
        },
        buttonValue() {
            if (this.newItem[3] === 'js|true') {
                return 'TRUE';
            }
            if (this.newItem[3] === 'js|false') {
                return 'FALSE';
            }
            if (this.newItem[3] === 'js|empty_array_object') {
                return 'Leeres JSON Array/Objekt';
            }
            if (this.newItem[3].startsWith('field|')) {
                return this.newItem[3].split('|')[1];
            }
            // e.g. "role|d8bb48c9-8702-40f8-a172-9b384d3a882e[Maintainer]"
            if (this.newItem[3].startsWith('role|')) {
                let match = this.newItem[3].match(/(?<=\[).+?(?=])/g);

                return Array.isArray(match) && match.length === 1 ? match[0] : this.newItem[3];
            }

            return '#';
        },
        selectedOperatorLabels() {
            if (this.newItem[1].startsWith('auth|process_role_npms')) {
                return Object.fromEntries(Object.entries(this.operatorLabels).filter(([key]) => [
                    'contains',
                    'not_contains'
                ].includes(key)));
            }

            return this.operatorLabels;
        },
        isRuleForRole(){
            return this.newItem[1] === 'auth|process_role_npms';
        }
    },
    methods: {
        changeValue(e) {
            let index = +e.target.dataset.index;
            let newItem = [...this.newItem];

            newItem[index] = e.target.dataset.value || e.target.value;
            this.newItem = newItem;
        },
        changeLeftValue(e){
            this.changeValue(e);

            // When the roles of the authenticated person are checked, we preselect "contains" as the operator
            if(this.newItem[1] === 'auth|process_role_npms') {
                this.newItem[2] = 'contains'
            }
        },
        addItem() {
            if (this.newItem[1] === '') {
                return;
            }

            this.$emit('display-rules-change', this.part, [
                ...this.displayRules,
                this.newItem
            ]);

            // Reset
            this.newItem = [
                'group_1',
                '',
                '=',
                ''
            ];
        },
    }

};
</script>

