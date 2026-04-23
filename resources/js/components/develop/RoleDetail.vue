<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="clearElementDetails">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <span class="nav-item nav-link p-2">
                        <span>{{ role.name.length >= 30 ? role.name.substring(0, 30) + '...' : role.name }}</span>
                    </span>
                </div>
            </nav>
            <div class="d-flex align-content-center pr-2 py-1" v-if="ui.editable">
                <button class="btn btn-sm btn-light" @click="openRoleModal(role)">
                    <span class="material-icons text-warning">edit</span>
                </button>
            </div>
        </div>
        <div class="detail-content panel-content-max-vh overflow-auto">
            <transition name="slide">
                <div v-if="isVisible" class="card-body p-0 pb-2 pr-2">
                    <span v-if="role.description" class="text-muted p-2 d-inline-block">{{ role.description }}&nbsp;</span>
                    <div v-if="generalPermissions.length">
                        <span class="p-2 d-inline-block">Allgemeine Rechte</span>
                        <table class="table mb-0">
                            <tbody>
                            <tr v-for="permission in generalPermissions" :key="permission.ident">
                                <td class="col-1 px-2 py-0 border-0">
                                    <span class="material-icons" :style="{color: getPermissionIconColor(permission.ident)}">done</span>
                                </td>
                                <td class="pl-2 py-0 pr-0 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ permission.name }}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="actionTypePermissions.length">
                        <hr/>
                        <span class="p-2 d-inline-block">Aktionen</span>
                        <table class="table mb-0">
                            <tbody>
                            <tr v-for="permission in sortedActionTypePermissions">
                                <td class="col-1 px-2 py-0 border-0">
                            <span class="material-icons"
                                  :style="{color: getPermissionIconColor(permission.ident)}">done</span>
                                </td>
                                <td class="pl-2 py-0 pr-0 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                    <span>{{
                                            actionType(identifierFromIdent(permission.ident)).name
                                        }} - {{ permission.name }}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="outputsPermissions.length">
                        <hr/>
                        <span class="p-2 d-inline-block">Prozess-Daten</span>
                        <table class="table mb-0">
                            <tbody>
                            <tr v-for="permission in sortedOutputsPermissions">
                                <td class="col-1 px-2 py-0 border-0">
                            <span class="material-icons"
                                  :style="{color: getPermissionIconColor(permission.ident)}">done</span>
                                </td>
                                <td class="pl-2 py-0 pr-0 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ identifierFromIdent(permission.ident) }}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="statusTypePermissions.length">
                        <hr/>
                        <span class="p-2 d-inline-block">Statustyp</span>
                        <table class="table mb-0">
                            <tbody>
                            <tr v-for="permission in sortedStatusTypePermissions">
                                <td class="col-1 px-2 py-0 border-0">
                            <span class="material-icons"
                                  :style="{color: getPermissionIconColor(permission.ident)}">done</span>
                                </td>
                                <td class="pl-2 py-0 pr-0 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ statusType(identifierFromIdent(permission.ident)).name }}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="listConfigsPermissions.length">
                        <hr/>
                        <span class="p-2 d-inline-block">Listen</span>
                        <table class="table mb-0">
                            <tbody>
                            <tr v-for="permission in sortedListConfigsPermissions">
                                <td class="col-1 px-2 py-0 border-0">
                            <span class="material-icons"
                                  :style="{color: getPermissionIconColor(permission.ident)}">done</span>
                                </td>
                                <td class="pl-2 py-0 pr-0 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ listConfig(identifierFromIdent(permission.ident)).name }}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="menuItemsPermissions.length">
                        <hr/>
                        <span class="p-2 d-inline-block">Menu-Einträge</span>
                        <table class="table mb-2">
                            <tbody>
                            <tr v-for="permission in sortedMenuItemsPermissions">
                                <td class="col-1 px-2 py-0 border-0">
                            <span class="material-icons"
                                  :style="{color: getPermissionIconColor(permission.ident)}">done</span>
                                </td>
                                <td class="pl-2 py-0 pr-0 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ menuItem(identifierFromIdent(permission.ident)).label }}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import Role from "./Role";

export default {
    components: {
        Role
    },
    props: {
        roleId: String
    },
    data() {
        return {
            isVisible: true
        };
    },
    computed: {
        ...mapGetters([
            'roles',
            'action_types',
            'status_types',
            'simulation',
            'definition',
            'menu_items',
            'list_configs',
            'outputs',
            'simulation',
            'ui'
        ]),
        role() {
            return this.roles.find(ele => ele.id === this.roleId);
        },
        generalPermissions() {
            return this.role.permissions.filter(ele => ele.ident.startsWith('process_type.process.'));
        },
        actionTypePermissions() {
            return this.role.permissions.filter(ele => ele.ident.startsWith('process_type.action_type.'));
        },
        statusTypePermissions() {
            return this.role.permissions.filter(ele => ele.ident.startsWith('process_type.status_type.'));
        },
        listConfigsPermissions() {
            return this.role.permissions.filter(ele => ele.ident.startsWith('process_type.list_config.'));
        },
        menuItemsPermissions() {
            return this.role.permissions.filter(ele => ele.ident.startsWith('process_type.menu_item.'));
        },
        outputsPermissions() {
            return this.role.permissions.filter(ele => ele.ident.startsWith('process_type.output.'));
        },
        sortedOutputsPermissions(){
            return this.outputsPermissions.sort((a, b) => a.ident.localeCompare(b.ident))
                .filter(permission =>  this.output(this.identifierFromIdent(permission.ident)))
        },
        sortedActionTypePermissions(){
            return this.actionTypePermissions.filter(permission =>  this.actionType(this.identifierFromIdent(permission.ident)))
                .sort((a, b) => this.actionType(this.identifierFromIdent(a.ident)).name.localeCompare(this.actionType(this.identifierFromIdent(b.ident)).name));
        },
        sortedStatusTypePermissions(){
            return this.statusTypePermissions.filter(permission =>  this.statusType(this.identifierFromIdent(permission.ident)))
                .sort((a, b) => this.statusType(this.identifierFromIdent(a.ident)).name.localeCompare(this.statusType(this.identifierFromIdent(b.ident)).name));
        },
        sortedListConfigsPermissions(){
            return this.listConfigsPermissions.filter(permission =>  this.listConfig(this.identifierFromIdent(permission.ident)))
                .sort((a, b) => this.listConfig(this.identifierFromIdent(a.ident)).name.localeCompare(this.listConfig(this.identifierFromIdent(b.ident)).name));
        },
        sortedMenuItemsPermissions(){
            return this.menuItemsPermissions.filter(permission =>  this.menuItem(this.identifierFromIdent(permission.ident)))
                .sort((a, b) => this.menuItem(this.identifierFromIdent(a.ident)).label.localeCompare(this.menuItem(this.identifierFromIdent(b.ident)).label));
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        openRoleModal(role) {
            this.openModal({
                componentName: 'RoleModal',
                data: {
                    role: role,
                    method: 'UpdateRole'
                }
            });
        },
        getPermissionIconColor(ident) {
            let permission = this.role.permissions.find(element => element.ident === ident);
            return permission.conditions.length > 0 ? '#f6993f' : '#38c172';
        },
        actionType(id) {
            return this.action_types.find(ele => ele.id === id);
        },
        statusType(id) {
            return this.status_types.find(ele => ele.id === id);
        },
        listConfig(id) {
            return this.list_configs.find(ele => ele.id === id);
        },
        menuItem(id) {
            return this.menu_items.find(ele => ele.id === id);
        },
        output(name) {
            return this.outputs.find(ele => ele.name === name);
        },
        identifierFromIdent(ident) {
            return ident.split('.')[2];
        }
    }
};
</script>
