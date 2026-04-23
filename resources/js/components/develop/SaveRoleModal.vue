<template>
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <ModalHeader :title="createMode ? 'Rolle erstellen' : role.name + ' - Rolle bearbeiten'"
                         docs-article="roles"
                         docs-section="create-role"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form>
                            <div class="form-group mb-2">
                                <label for="name" class="mb-0">Name</label>
                                <input type="text" class="form-control form-control-sm" id="name" v-model="role.name"
                                       aria-describedby="name" required>
                                <div v-for="error in (ui.validationErrors.name || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="description" class="mb-0">Beschreibung</label>
                                <textarea class="form-control form-control-sm" v-model="role.description"
                                          id="description" aria-describedby="description"></textarea>
                                <div v-for="error in (ui.validationErrors.description || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col d-flex justify-content-end">
                                <div>
                                    <button @click="toggleVisibilityAllOn" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Alle ausklappen</small>
                                    </button>
                                    <button @click="toggleVisibilityAllOff" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Alle einklappen</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="mb-2">
                                <span class="material-icons mr-2 mouse-pointer"
                                      @click="toggleVisibility('generalPermissions')">
                                    {{ visibility.generalPermissions ? 'expand_more' : 'chevron_right' }}
                                </span>
                                <span @click="toggleVisibility('generalPermissions')" class="mouse-pointer">Allgemeine Rechte</span>
                                <button @click="toggleAllOn(availableGeneralPermissions.map(ele => ele.ident))"
                                        class="btn btn-sm px-1 p-0 btn-light">
                                    <small>Alle</small>
                                </button>
                                <button @click="toggleAllOff(generalPermissions)" class="btn btn-sm px-1 p-0 btn-light">
                                    <small>Keine</small>
                                </button>
                            </div>
                        </div>
                        <div v-if="visibility.generalPermissions" class="card-body p-0 pb-2 pr-2">
                            <form>
                                <div class="input-group" v-for="permission in availableGeneralPermissions">
                                    <div class="custom-control custom-switch py-1">
                                        <input type="checkbox" :name="permission.ident" :id="permission.ident"
                                               @click="togglePermission(permission.ident, getDeselectedIdentities(permission))"
                                               class="custom-control-input" value="1"
                                               :checked="getPermissionObject(permission.ident)">
                                        <label class="custom-control-label" :for="permission.ident">{{
                                                permission.name
                                            }}
                                        </label>
                                    </div>
                                    <div v-if="getPermissionObject(permission.ident)" class="custom-control">
                                        <button
                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(permission.ident).conditions.length ? 'active' : '')"
                                            @click="openConditions(permission.ident)">
                                            <span class="material-icons">flash_on</span>
                                        </button>
                                    </div>
                                    <div class="ml-4" style="flex: 100%;" v-if="permission.sub_permissions">
                                        <div class="input-group" v-for="sub_permission in permission.sub_permissions">
                                            <div class="custom-control custom-switch py-1">
                                                <input type="checkbox" :name="sub_permission.ident"
                                                       :id="sub_permission.ident"
                                                       @click="togglePermission(sub_permission.ident, getDeselectedIdentities(sub_permission,[permission.ident]))"
                                                       class="custom-control-input" value="1"
                                                       :checked="getPermissionObject(sub_permission.ident)">
                                                <label class="custom-control-label"
                                                       :for="sub_permission.ident">{{ sub_permission.name }}
                                                </label>
                                            </div>
                                            <div v-if="getPermissionObject(sub_permission.ident)"
                                                 class="custom-control">
                                                <button
                                                    :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(sub_permission.ident).conditions.length ? 'active' : '')"
                                                    @click="openConditions(sub_permission.ident)">
                                                    <span class="material-icons">flash_on</span>
                                                </button>
                                            </div>
                                            <div class="ml-4" style="flex: 100%;" v-if="sub_permission.sub_permissions">
                                                <div class="input-group"
                                                     v-for="sub_sub_permission in sub_permission.sub_permissions">
                                                    <div class="custom-control custom-switch py-1">
                                                        <input type="checkbox" :name="sub_sub_permission.ident"
                                                               :id="sub_sub_permission.ident"
                                                               @click="togglePermission(sub_sub_permission.ident, getDeselectedIdentities(sub_sub_permission,[sub_permission.ident,permission.ident]))"
                                                               class="custom-control-input" value="1"
                                                               :checked="getPermissionObject(sub_sub_permission.ident)">
                                                        <label class="custom-control-label"
                                                               :for="sub_sub_permission.ident">{{
                                                                sub_sub_permission.name
                                                            }}
                                                        </label>
                                                    </div>
                                                    <div v-if="getPermissionObject(sub_sub_permission.ident)"
                                                         class="custom-control">
                                                        <button
                                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(sub_sub_permission.ident).conditions.length ? 'active' : '')"
                                                            @click="openConditions(sub_sub_permission.ident)">
                                                            <span class="material-icons">flash_on</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted d-inline-block mt-2">Diese Rechte überschreiben die
                                    spezifischeren Rechte der Kategorien.</small>
                            </form>
                        </div>
                        <div v-if="action_types.length">
                            <hr class="my-3"/>
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="material-icons mr-2 mouse-pointer"
                                          @click="toggleVisibility('actionTypes')">
                                    {{ visibility.actionTypes ? 'expand_more' : 'chevron_right' }}
                                    </span>
                                    <span @click="toggleVisibility('actionTypes')" class="mouse-pointer">Aktionen</span>
                                    <div v-if="visibility.actionTypes" class="d-inline-block">
                                        <button
                                            @click="toggleAllOn(action_types.flatMap(action => availableActionTypePermissions.map(permission => getPermissionIdentityFromTemplate(permission.ident, action.id))))"
                                            class="btn btn-sm px-1 p-0 btn-light">
                                            <small>Alle</small>
                                        </button>
                                        <button @click="toggleAllOff(actionTypePermissions)"
                                                class="btn btn-sm px-1 p-0 btn-light">
                                            <small>Keine</small>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="visibility.actionTypes">
                                    <button @click="toggleVisibilityAllActionTypesOn" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Alle ausklappen</small>
                                    </button>
                                    <button @click="toggleVisibilityAllActionTypesOff" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Alle einklappen</small>
                                    </button>
                                </div>
                            </div>
                            <form v-if="visibility.actionTypes" class="mt-2">
                                <div class="input-group mb-3 ml-3" v-for="actionType in [...action_types].sort((a, b) => a.name.localeCompare(b.name))">
                                    <span class="d-block mouse-pointer mb-1">
                                        <span class="material-icons mr-2"
                                              @click="toggleVisibilityActionType(actionType.reference)">
                                            {{
                                                visibility.actionType.includes(actionType.reference) ? 'expand_more' : 'chevron_right'
                                            }}
                                        </span>
                                        <span @click="toggleVisibilityActionType(actionType.reference)">
                                            {{ actionType.name }} - <span class="text-muted"> {{
                                                actionType.reference
                                            }}</span>
                                        </span>
                                    </span>
                                    <div :class="'w-100 pb-2 border-bottom ' + (visibility.actionType.includes(actionType.reference) ? 'd-block' : 'd-none')">
                                        <div class="input-group"
                                            v-for="permission in getPermissionsFromTemplate(availableActionTypePermissions, actionType.id)">
                                            <div class="custom-control custom-switch py-1">
                                                <input type="checkbox" :name="permission.ident" :id="permission.ident"
                                                       @click="togglePermission(permission.ident, getDeselectedIdentities(permission, null, actionType.id))"
                                                       class="custom-control-input" value="1"
                                                       :checked="getPermissionObject(permission.ident)">
                                                <label class="custom-control-label"
                                                       :for="permission.ident">{{ permission.name }}
                                                </label>
                                            </div>
                                            <div v-if="getPermissionObject(permission.ident)" class="custom-control">
                                                <button
                                                    :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(permission.ident).conditions.length ? 'active' : '')"
                                                    @click="openConditions(permission.ident)">
                                                    <span class="material-icons">flash_on</span>
                                                </button>
                                            </div>
                                            <div class="ml-4" style="flex: 100%;" v-if="permission.sub_permissions">
                                                <div class="input-group"
                                                     v-for="sub_permission in getPermissionsFromTemplate(permission.sub_permissions, actionType.id)">
                                                    <div class="custom-control custom-switch py-1">
                                                        <input type="checkbox" :name="sub_permission.ident"
                                                               :id="sub_permission.ident"
                                                               @click="togglePermission(sub_permission.ident, getDeselectedIdentities(sub_permission,[permission.ident], actionType.id))"
                                                               class="custom-control-input" value="1"
                                                               :checked="getPermissionObject(sub_permission.ident)">
                                                        <label class="custom-control-label" :for="sub_permission.ident">{{
                                                                sub_permission.name
                                                            }}
                                                        </label>
                                                    </div>
                                                    <div v-if="getPermissionObject(sub_permission.ident)"
                                                         class="custom-control">
                                                        <button
                                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(sub_permission.ident).conditions.length ? 'active' : '')"
                                                            @click="openConditions(sub_permission.ident)">
                                                            <span class="material-icons">flash_on</span>
                                                        </button>
                                                    </div>
                                                    <div class="ml-4" style="flex: 100%;"
                                                         v-if="sub_permission.sub_permissions">
                                                        <div class="input-group"
                                                             v-for="sub_sub_permission in getPermissionsFromTemplate(sub_permission.sub_permissions, actionType.id)">
                                                            <div class="custom-control custom-switch py-1">
                                                                <input type="checkbox" :name="sub_sub_permission.ident"
                                                                       :id="sub_sub_permission.ident"
                                                                       @click="togglePermission(sub_sub_permission.ident, getDeselectedIdentities(sub_sub_permission,[sub_permission.ident,permission.ident], actionType.id))"
                                                                       class="custom-control-input" value="1"
                                                                       :checked="getPermissionObject(sub_sub_permission.ident)">
                                                                <label class="custom-control-label"
                                                                       :for="sub_sub_permission.ident">{{
                                                                        sub_sub_permission.name
                                                                    }}
                                                                </label>
                                                            </div>
                                                            <div v-if="getPermissionObject(sub_sub_permission.ident)"
                                                                 class="custom-control">
                                                                <button
                                                                    :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(sub_sub_permission.ident).conditions.length ? 'active' : '')"
                                                                    @click="openConditions(sub_sub_permission.ident)">
                                                                    <span class="material-icons">flash_on</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-if="outputs.length">
                            <hr class="my-3"/>
                            <div class="mb-2">
                                <span class="material-icons mr-2" style="cursor: pointer; "
                                      @click="toggleVisibility('outputs')">
                                    {{ visibility.outputs ? 'expand_more' : 'chevron_right' }}
                                </span>
                                <span @click="toggleVisibility('outputs')" class="mouse-pointer">Daten</span>
                                <div v-if="visibility.outputs" class="d-inline-block">
                                    <button
                                        @click="toggleAllOn(outputs.map(ele => 'process_type.output.' + ele.name + '.view'))"
                                        class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Alle</small>
                                    </button>
                                    <button @click="toggleAllOff(outputsPermissions)" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Keine</small>
                                    </button>
                                </div>
                            </div>
                            <form v-if="visibility.outputs">
                                <div class="input-group" v-for="output in [...outputs].sort((a, b) => a.name.localeCompare(b.name))">
                                    <div class="custom-control custom-switch py-1">
                                        <input type="checkbox" :name="`process_type.output.${output.name}.view`"
                                               :id="`process_type.output.${output.name}.view`"
                                               class="custom-control-input" value="1"
                                               @click="togglePermission(`process_type.output.${output.name}.view`)"
                                               :checked="getPermissionObject(`process_type.output.${output.name}.view`)">
                                        <label class="custom-control-label"
                                               :for="`process_type.output.${output.name}.view`">{{
                                                output.name
                                            }}
                                        </label>
                                    </div>
                                    <div v-if="getPermissionObject(`process_type.output.${output.name}.view`)"
                                         class="custom-control">
                                        <button
                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(`process_type.output.${output.name}.view`).conditions.length ? 'active' : '')"
                                            @click="openConditions(`process_type.output.${output.name}.view`)">
                                            <span class="material-icons">flash_on</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-if="status_types.length">
                            <hr class="my-3"/>
                            <div class="mb-2">
                                <span class="material-icons mr-2" style="cursor: pointer; "
                                      @click="toggleVisibility('statusTypes')">
                                {{ visibility.statusTypes ? 'expand_more' : 'chevron_right' }}
                                </span>
                                <span @click="toggleVisibility('statusTypes')" class="mouse-pointer">Statustypen</span>
                                <div v-if="visibility.statusTypes" class="d-inline-block">
                                    <button
                                        @click="toggleAllOn(status_types.map(ele => 'process_type.status_type.' + ele.id + '.view'))"
                                        class="btn btn-sm px-1 p-0 btn-light"><small>Alle</small>
                                    </button>
                                    <button @click="toggleAllOff(statusTypePermissions)" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Keine</small>
                                    </button>
                                </div>
                            </div>
                            <form v-if="visibility.statusTypes">
                                <div class="input-group" v-for="statusType in [...status_types].sort((a, b) => a.name.localeCompare(b.name))">
                                    <div class="custom-control custom-switch py-1">
                                        <input type="checkbox" :name="`process_type.status_type.${statusType.id}.view`"
                                               :id="`process_type.status_type.${statusType.id}.view`"
                                               class="custom-control-input" value="1"
                                               @click="togglePermission(`process_type.status_type.${statusType.id}.view`)"
                                               :checked="getPermissionObject(`process_type.status_type.${statusType.id}.view`)">
                                        <label class="custom-control-label"
                                               :for="`process_type.status_type.${statusType.id}.view`">{{
                                                statusType.name
                                            }} <span class="text-muted"> - {{
                                                    statusType.reference
                                                }}</span></label>
                                    </div>
                                    <div v-if="getPermissionObject(`process_type.status_type.${statusType.id}.view`)"
                                         class="custom-control">
                                        <button
                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(`process_type.status_type.${statusType.id}.view`).conditions.length ? 'active' : '')"
                                            @click="openConditions(`process_type.status_type.${statusType.id}.view`)">
                                            <span class="material-icons">flash_on</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-if="list_configs.length">
                            <hr class="my-3"/>
                            <div class="mb-2">
                                <span class="material-icons mr-2" style="cursor: pointer; "
                                      @click="toggleVisibility('listConfigs')">
                                    {{ visibility.listConfigs ? 'expand_more' : 'chevron_right' }}
                                </span>
                                <span @click="toggleVisibility('listConfigs')" class="mouse-pointer">Listen</span>
                                <div v-if="visibility.listConfigs" class="d-inline-block">
                                    <button
                                        @click="toggleAllOn(list_configs.map(ele => 'process_type.list_config.' + ele.id + '.view'))"
                                        class="btn btn-sm px-1 p-0 btn-light"><small>Alle</small>
                                    </button>
                                    <button @click="toggleAllOff(listConfigsPermissions)" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Keine</small>
                                    </button>
                                </div>
                            </div>
                            <form v-if="visibility.listConfigs">
                                <div class="input-group" v-for="listConfig in [...list_configs].sort((a, b) => a.name.localeCompare(b.name))">
                                    <div class="custom-control custom-switch py-1">
                                        <input type="checkbox" :name="`process_type.list_config.${listConfig.id}.view`"
                                               :id="`process_type.list_config.${listConfig.id}.view`"
                                               class="custom-control-input" value="1"
                                               @click="togglePermission(`process_type.list_config.${listConfig.id}.view`)"
                                               :checked="getPermissionObject(`process_type.list_config.${listConfig.id}.view`)">
                                        <label class="custom-control-label"
                                               :for="`process_type.list_config.${listConfig.id}.view`">{{
                                                listConfig.name
                                            }}
                                            <span class="text-muted"> - {{ listConfig.slug }}</span>
                                        </label>
                                    </div>
                                    <div v-if="getPermissionObject(`process_type.list_config.${listConfig.id}.view`)"
                                         class="custom-control">
                                        <button
                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(`process_type.list_config.${listConfig.id}.view`).conditions.length ? 'active' : '')"
                                            @click="openConditions(`process_type.list_config.${listConfig.id}.view`)">
                                            <span class="material-icons">flash_on</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-if="menu_items.length">
                            <hr class="my-3"/>
                            <div class="mb-2">
                                <span class="material-icons mr-2 mouse-pointer"
                                      @click="toggleVisibility('menuItems')">
                                    {{ visibility.menuItems ? 'expand_more' : 'chevron_right' }}
                                </span>
                                <span @click="toggleVisibility('menuItems')" class="mouse-pointer">Menü-Einträge</span>
                                <div v-if="visibility.menuItems" class="d-inline-block">
                                    <button
                                        @click="toggleAllOn(menu_items.map(ele => 'process_type.menu_item.' + ele.id + '.view'))"
                                        class="btn btn-sm px-1 p-0 btn-light"><small>Alle</small>
                                    </button>
                                    <button @click="toggleAllOff(menuItemsPermissions)" class="btn btn-sm px-1 p-0 btn-light">
                                        <small>Keine</small>
                                    </button>
                                </div>
                            </div>
                            <form v-if="visibility.menuItems">
                                <div class="input-group" v-for="menuItem in [...menu_items].sort((a, b) => a.label.localeCompare(b.label))">
                                    <div class="custom-control custom-switch py-1">
                                        <input type="checkbox" :name="`process_type.menu_item.${menuItem.id}.view`"
                                               :id="`process_type.menu_item.${menuItem.id}.view`"
                                               class="custom-control-input" value="1"
                                               @click="togglePermission(`process_type.menu_item.${menuItem.id}.view`)"
                                               :checked="getPermissionObject(`process_type.menu_item.${menuItem.id}.view`)">
                                        <label class="custom-control-label"
                                               :for="`process_type.menu_item.${menuItem.id}.view`">{{
                                                menuItem.label
                                            }}</label>
                                    </div>
                                    <div v-if="getPermissionObject(`process_type.menu_item.${menuItem.id}.view`)"
                                         class="custom-control">
                                        <button
                                            :class="'btn btn-sm btn-outline-warning p-0 px-1 ' + (getPermissionObject(`process_type.menu_item.${menuItem.id}.view`).conditions.length ? 'active' : '')"
                                            @click="openConditions(`process_type.menu_item.${menuItem.id}.view`)">
                                            <span class="material-icons">flash_on</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" v-on="$listeners" :on-save="onSave" @cancel="onCancel"
                         :save-label="createMode ? 'Erstellen' : 'Speichern'"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import permissions from '../../permissions';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";

export default {
    name: 'SaveRoleModal',
    components: {
        ModalHeader,
        ModalFooter
    },
    props: {
        data: Object
    },
    data() {
        return {
            role: {
                id: null,
                name: null,
                description: null,
                permissions: []
            },
            visibility: {
                generalPermissions: true,
                actionTypes: false,
                outputs: false,
                statusTypes: false,
                listConfigs: false,
                menuItems: false, // Array of visible actiontype references.
                actionType: [],
            },
            createMode: true,
            availableGeneralPermissions: permissions.general,
            availableActionTypePermissions: permissions.action_type,
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'roles',
            'action_types',
            'status_types',
            'list_configs',
            'menu_items',
            'outputs'
        ]),
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
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            this.patchDefinition(this.ui.modal.data.method, this.role).then(this.closeModal).catch(() => {
            });
        },
        toggleVisibility(property) {
            this.visibility[property] = !this.visibility[property]; // Umschalten der Sichtbarkeit
        },
        togglePermission(identity, deselectedIdentities = []) {
            if (this.getPermissionObject(identity)) {
                this.role.permissions = this.role.permissions.filter(ele => ele.ident !== identity);
            }
            else {
                this.role.permissions.push({
                    ident: identity,
                    conditions: []
                });
                if (deselectedIdentities.length) {
                    this.role.permissions = this.role.permissions.filter(ele => !deselectedIdentities.includes(ele.ident));
                }
            }
        },
        toggleVisibilityAllOn() {
            Object.keys(this.visibility).filter(ele => ele !== 'actionType').forEach((permission) => {
                this.visibility[permission] = true;
            });
        },
        toggleVisibilityAllOff() {
            Object.keys(this.visibility).filter(ele => ele !== 'actionType').forEach((permission) => {
                this.visibility[permission] = false;
            });
        },
        toggleAllOn(permissionIdents) {
            let that = this;
            permissionIdents.forEach(function (permissionIdent) {
                if (!that.getPermissionObject(permissionIdent)) {
                    that.role.permissions.push({
                        ident: permissionIdent,
                        conditions: []
                    });
                }
            });
        },
        toggleVisibilityActionType(ref) {
            this.visibility.actionType = this.visibility.actionType.includes(ref) ? this.visibility.actionType.filter(ele => ele !== ref) : [
                ...this.visibility.actionType,
                ref
            ];
        },
        toggleVisibilityAllActionTypesOn(){
            this.visibility.actionType = this.action_types.map(ele => ele.reference)
        },
        toggleVisibilityAllActionTypesOff(){
            this.visibility.actionType = []
        },
        toggleAllOff(permissionIdents) {
            this.role.permissions = [...this.role.permissions.filter(permission => !permissionIdents.find(permissionIdent => permissionIdent.ident === permission.ident))];
        },
        getPermissionObject(ident) {
            return this.role.permissions.find(element => element.ident === ident);
        },
        getDescendantsIdentities(permission, identities, actionType = "") {
            if (permission.sub_permissions) {
                for (const subPermission of this.getPermissionsFromTemplate(permission.sub_permissions, actionType)) {
                    identities.push(subPermission.ident);
                    if (subPermission.sub_permissions) {
                        this.getDescendantsIdentities(subPermission, identities, actionType);
                    }
                }
            }
            return identities;
        },
        getDeselectedIdentities(permission, parents = [], actionType = "") {
            return (this.getDescendantsIdentities(permission, [], actionType)).concat(parents);
        },
        getPermissionIdentityFromTemplate(permissionIdentity, id) {
            return permissionIdentity.replace('*', id);
        },
        getPermissionsFromTemplate(permissions, id) {
            let that = this;
            // To clone the objects (Deep copy).
            let copiedPermissions = permissions.map(item => {
                return {...item};
            });
            return copiedPermissions.map(function (permission) {
                permission.ident = that.getPermissionIdentityFromTemplate(permission.ident, id);
                return permission;
            });
        },
        openConditions(ident) {
            this.$emit('open-conditions', this.getPermissionObject(ident));
        },
        onCancel() {
            this.closeModal();
        }
    },
    watch: {
        role: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        data: {
            handler: function (value) {
                if (value.hasOwnProperty('ident')) {
                    let index = this.role.permissions.findIndex(permission => permission.ident === value.ident);
                    this.role.permissions[index].conditions = value.conditions;
                }
            },
            deep: true
        }
    },
    mounted() {
        if (this.ui.modal.data.role) {
            this.createMode = false;
            this.role = {
                ...this.role, ...this.ui.modal.data.role,
                permissions: this.ui.modal.data.role.permissions.map(ele => ({
                    ident: ele.ident,
                    conditions: ele.conditions
                }))
            };
        }
    },
};
</script>