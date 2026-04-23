<template>
    <div>
        <div>
            <div class="p-1">
                <table class="table table-sm table-borderless m-0">
                    <tbody>
                    <tr class="d-flex">
                        <td class="py-0 w-25 mb-0">
                            <span class="text-muted">Prozesse</span>
                        </td>
                        <td class="py-0 w-25 mb-0">
                            <span class="pl-2 text-muted">Rolle</span>
                        </td>
                        <td v-if="withActions" class="py-0 w-60 mb-0">
                            <span class="pl-2 text-muted">Aktionen</span>
                        </td>
                        <td v-else class="py-0 w-60 mb-0"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <template v-for="(roles, groupKey) in rolesByGroup">
                <div class="p-1">
                    <span class="p-1 text-primary">
                        <small>{{ groupLabels[groupKey] }}</small>
                    </span>
                    <table class="table table-sm table-borderless m-0">
                        <tbody>
                        <template v-for="(role, index) in roles">
                            <tr class="d-flex bg-light">
                                <td class="py-0 w-25 mb-0">
                                    <span>{{ role.processTypeMeta }}</span>
                                </td>
                                <td class="py-0 w-25 mb-0">
                                    <span>{{ role.name }}</span>
                                </td>
                                <td v-if="withActions" class="py-0 w-50 mb-0">
                                    <div v-if="(role.actionTypes || []).length > 0" class="d-flex flex-column">
                                        <template v-for="action in role.actionTypes">
                                            <span class="text-overflow-ellipsis overflow-hidden"
                                                  :title="action">{{ action }}</span>
                                        </template>
                                    </div>
                                    <div v-else class="d-flex flex-column pt-1">
                                        <small class="text-muted">Beliebige Aktion ausführbar.</small>
                                    </div>
                                </td>
                                <td v-else class="py-0 w-50 mb-0">

                                </td>
                                <td class="py-0 w-10 mb-0">
                                    <button class="btn btn-sm btn-light float-right" @click="deleteRole(role)">
                                        <span class="material-icons text-danger">close</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="index !== roles.length - 1">
                                <td class="text-muted p-0 pl-1 py-1">
                                    <small>und</small>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
                <span v-if="!lastIndex(Object.keys(rolesByGroup), groupKey)"
                      class="text-muted d-block pl-3 py-1 border-top border-bottom">
                    <small>oder</small>
                </span>
            </template>
        </div>
        <div class="input-group input-group-sm my-2 d-flex flex-row align-items-stretch">
            <div class="input-group-prepend">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    {{ groupLabels[newRole.group] }}
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button v-for="index in 20" :key="index" type="button" class="dropdown-item"
                            @click="changeValue('group','g_' + index)">
                        Gruppe {{ index }}
                    </button>
                </div>
            </div>
            <div class="input-group-append">
                <button class="btn btn-sm dropdown-toggle btn-outline-primary" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    {{ newRole.name || '#' }}
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <template v-for="(processTypeMeta) in Object.keys(processTypeMetasRoles)">
                        <label class="px-4 w-100 font-weight-bold">
                            {{ processTypeMeta }}
                        </label>
                        <button v-for="(role) in processTypeMetasRoles[processTypeMeta]" type="button"
                                class="dropdown-item" @click="changeRole(role.id, role.name, processTypeMeta)">
                            {{ role.name }}
                        </button>
                    </template>
                </div>
            </div>
            <div v-if="withActions && newRole.name" class="input-group-append w-55">
                <vue-tags-input v-model="tag" :tags="tags" :autocomplete-items="processTypeMetasActions"
                                :autocomplete-min-length="0" :save-on-key="[13, ';']" :add-only-from-autocomplete="true"
                                @tags-changed="tagsChanged">
                    <div slot="autocomplete-item" slot-scope="props" :class="'d-flex justify-content-between p-0'">
                    <span class="flex-grow-1 px-2 py-1" @click="props.performAdd(props.item)">
                        <span>{{ props.item.text }}</span>
                    </span>
                    </div>
                </vue-tags-input>
            </div>
            <div class="input-group-append">
                <button class="btn btn-sm rounded-right btn-outline-success" @click="addRole">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import VueTagsInput from '@johmun/vue-tags-input';

export default {
    components: {
        VueTagsInput
    },
    props: {
        processTypeMetasRolesSupportData: Object,
        processTypeMetasActionsSupportData: Object,
        processTypeWhereIn: Array,
        userInvolvementRoles: Array,
        withActions: Boolean,
    },
    data() {
        let gLabels = {};
        for (let i = 1; i <= 20; i++) {
            gLabels['g_' + i] = 'Gruppe ' + i;
        }

        return {
            newRole: {
                'group': 'g_1',
                'id': '',
                'name': '',
                'processTypeMeta': '',
                'actionTypes': []
            },
            groupLabels: gLabels,
            roles: [...this.userInvolvementRoles],
            tag: '',
            tags: []
        };
    },
    computed: {
        processTypeMetasRoles() {
            return Object.keys(this.processTypeMetasRolesSupportData)
                .filter(key => this.processTypeWhereIn.includes(key))
                .reduce((obj, key) => {
                    return {
                        ...obj,
                        [key]: this.processTypeMetasRolesSupportData[key]
                    };
                }, {});
        },
        processTypeMetasActions() {
            const filtered = Object.keys(this.processTypeMetasActionsSupportData)
                .filter(key => this.processTypeWhereIn.includes(key))
                .reduce((obj, key) => {
                    return {
                        ...obj,
                        [key]: this.processTypeMetasActionsSupportData[key]
                    };
                }, {});

            return filtered[this.newRole.processTypeMeta]?.map(item => ({
                text: item.reference,
                value: item
            })) || [];
        },
        rolesByGroup() {
            let grouped = {};

            for (let i = 0; i < this.roles.length; i++) {
                let groupName = this.roles[i]['group'];

                if (grouped.hasOwnProperty(groupName)) {
                    grouped[groupName].push(this.roles[i]);
                }
                else {
                    grouped[groupName] = [this.roles[i]];
                }
            }

            // Sort by group number
            grouped = Object.keys(grouped).sort().reduce((obj, key) => ({
                ...obj,
                [key]: grouped[key]
            }), {});

            return grouped;
        },
    },
    methods: {
        changeValue(field, value) {
            this.newRole[field] = value;
        },
        changeRole(id, name, processTypeMeta) {
            this.newRole.id = id;
            this.newRole.name = name;
            this.newRole.processTypeMeta = processTypeMeta;
        },
        addRole() {
            if (this.newRole.id === '') {
                return;
            }
            this.roles.push(this.newRole);
            this.newRole = {
                'group': 'g_1',
                'id': '',
                'name': '',
                'processTypeMeta': '',
                'actionTypes': []
            };
            this.$emit('update-roles-selection', this.roles);
        },
        deleteRole(role) {
            this.roles = this.roles.filter((element) => element !== role);
            this.$emit('update-roles-selection', this.roles);
        },
        lastIndex(items, key) {
            return items.indexOf(key) === items.length - 1;
        },
        tagsChanged(newItems) {
            this.newRole.actionTypes = newItems.map(i => i.value.reference);
        }
    }
};
</script>
