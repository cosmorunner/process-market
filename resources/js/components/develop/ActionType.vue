<template>
    <div class="card mb-0 rounded-0 border-left-0 border-right-0 border-bottom-0 action-type">
        <div
            class="actiontype-header card-header border-bottom-0 d-flex align-items-center bg-white justify-content-between px-2 py-1"
            v-if="!hideHeader">
            <template v-if="!simulation.running && ($parent.editingId !== actionType.id)">
                <div class="d-inline-flex align-items-center">
                    <button type="button" class="btn btn-link p-0 text-left"
                            @click="showActionTypeDetail(actionType.id)">
                        <span>{{ actionType.name }}</span>
                    </button>
                    <button type="button" class="quick-edit-btn btn btn-sm btn-light ml-2 d-none"
                            @click="enableEditing(actionType)" v-if="!simulation.running && editable">
                        <span class="material-icons text-warning">edit</span>
                    </button>
                </div>
            </template>
            <div v-if="!simulation.running && $parent.editingId === actionType.id" class="d-flex">
                <input :ref="actionType.reference" v-model="editableName" @keyup.enter="saveName"
                       class="form-control form-control-sm" @keyup.esc="cancelEditing(actionType.name)"/>
                <button type="button" class="btn btn-sm btn-light ml-2 border" @click="saveName" v-if="editableName">
                    <span class="material-icons text-success">done</span>
                </button>
                <button type="button" class="btn btn-sm btn-light ml-1 border" @click="cancelEditing(actionType.name)">
                    <span class="material-icons text-danger">close</span>
                </button>
            </div>
            <div v-if="simulation.running" class="btn mouse-pointer p-0 pr-1" data-toggle="collapse"
                 :data-target="'#collapse-action-' + actionType.id" @click="toggleContent">
                     <span class="material-icons">
                      {{ isCollapsed ? 'keyboard_arrow_up' : 'keyboard_arrow_down' }}
                    </span>
                <span>{{ actionType.name }}</span>
            </div>
            <div class="text-nowrap">
                <button class="btn btn-sm" disabled v-if="actionType.action_rules.length && !simulation.running">
                    <span class="material-icons text-actionrule">circle</span>
                </button>
                <button class="btn btn-sm" disabled v-if="actionType.status_rules.length && !simulation.running">
                    <span class="material-icons text-statusrule">circle</span>
                </button>
                <button class="btn btn-sm" disabled v-else>
                    <span class="material-icons text-white">circle</span>
                </button>
                <a :href="configUrl('ActionTypes', 'Components', actionType.id)" class="btn btn-sm btn-light"
                   v-if="!simulation.running">
                    <span class="material-icons">list_alt</span>
                </a>
                <div v-if="!simulation.running && ui.editable" class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-light" type="button" id="actionDropDownButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">more_vert</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="actionDropDownButton">
                        <button v-on:click="openActionTypeModal(actionType)" class="dropdown-item" type="button">
                            Bearbeiten
                        </button>
                        <button v-on:click="onCopyActionType(actionType.id)" class="dropdown-item" type="button">
                            Kopieren
                        </button>
                        <div class="dropdown-divider"></div>
                        <button v-on:click="onDeleteActionType(actionType.id)" class="dropdown-item text-danger"
                                type="button">Löschen
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="simulation.running">
                <!-- Ausführen Button -->
                <button
                    :class="'btn btn-sm ' + (simulation.executing !== actionType.id ? 'btn-success' : 'btn-warning')"
                    type="button" @click="handleExecAction(actionType)" :disabled="simulation.executing !== null"
                    v-if="active_action_type_ids.includes(actionType.id) && !inaccessible_action_type_ids.includes(actionType.id)">
                    <span v-if="simulation.executing !== actionType.id"><span
                        class="material-icons">chevron_right</span> Ausführen</span>
                    <span v-if="simulation.executing === actionType.id"><span class="material-icons">more_horiz</span> Laden...</span>
                </button>
                <!-- Keine Berechtigung Button -->
                <button class="btn btn-sm btn-warning" type="button" disabled
                        v-if="inaccessible_action_type_ids.includes(actionType.id)">
                    <span><span class="material-icons">lock</span> Keine Berechtigung</span>
                </button>
                <!-- Inaktiv -->
                <button class="btn btn-sm btn-danger" type="button" disabled
                        v-if="!inaccessible_action_type_ids.includes(actionType.id) && !active_action_type_ids.includes(actionType.id)">
                    <span><span class="material-icons">block</span> Inaktiv</span>
                </button>
            </div>
        </div>
        <div class="card-body p-0" v-if="showRules">
            <template>
                <div :id="'collapse-action-' + actionType.id"
                     :class="'transition-all duration-300 ' + (useRulesCollapse ? 'collapse' : '')">
                    <table class="table mb-0" v-if="actionType.action_rules.length">
                        <tbody>
                        <template v-for="(actionRules, groupName) in groupedActionRules"
                                  v-if="Object.keys(groupedActionRules).length > 1">
                            <div
                                :class="'card rounded-0 border-left-0 border-right-0 ' + (lastIndex(Object.keys(groupedActionRules), groupName) ? 'border-bottom-0 ' : '') + (firstIndex(Object.keys(groupedActionRules), groupName) ? 'border-top-0' : '')">
                                <div class="card-header px-1 py-0">
                                    <small class="text-muted">{{ groupLabels[groupName] }}</small>
                                </div>
                                <div class="card-body p-0">
                                    <template v-for="(actionRule, index) in actionRules">
                                        <ActionRule :action-rule="actionRule"
                                                    :status-type="statusType(actionRule.status_type_id)"
                                                    :hide-delete-button="hideDeleteButtons"
                                                    :editable="editable && ui.editable"/>
                                        <small v-if="index !== actionRules.length - 1"
                                               class="pl-2 text-muted">und</small>
                                    </template>
                                </div>
                            </div>
                            <small v-if="!lastIndex(Object.keys(groupedActionRules), groupName)"
                                   class="text-muted pl-2 pt-2 pb-1 d-block">oder</small>
                        </template>
                        <template v-else>
                            <template v-for="(actionRule, index) in actionRules">
                                <ActionRule :action-rule="actionRule"
                                            :status-type="statusType(actionRule.status_type_id)"
                                            :hide-delete-button="hideDeleteButtons"
                                            :editable="editable && ui.editable"/>
                                <small v-if="index !== actionRules.length - 1" class="pl-2 text-muted">und</small>
                            </template>
                        </template>
                        </tbody>
                    </table>
                    <div class="p-2 text-muted"
                         v-if="!actionType.action_rules.length && actionType.status_rules.length">
                        <small>Keine Aktionsregeln hinzugefügt. Die Aktion ist in jeder Situation ausführbar.</small>
                        <span class="material-icons" data-toggle="tooltip" data-placement="top"
                              title="Rechtsklick auf eine Status-Fläche im Graphen um eine Aktionsregel hinzuzufügen.">help</span>
                    </div>
                    <hr class="my-3" v-if="actionType.action_rules.length || actionType.status_rules.length"/>
                    <table class="table mb-0" v-if="actionType.status_rules.length">
                        <tbody>
                        <template v-for="statusRule in actionType.status_rules">
                            <StatusRule :status-rule="statusRule" :status-type="statusType(statusRule.status_type_id)"
                                        :hide-delete-button="hideDeleteButtons" :editable="editable && ui.editable"/>
                        </template>
                        </tbody>
                    </table>
                    <div class="px-2 pb-2 text-muted"
                         v-if="!actionType.status_rules.length && actionType.action_rules.length">
                        <small>Keine Statusregeln hinzugefügt. Die Aktion verändert die Situation nicht.</small>
                        <span class="material-icons" data-toggle="tooltip" data-placement="top"
                              title="Rechtsklick auf eine Status-Fläche im Graphen um eine Statusregeln hinzuzufügen.">help</span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ActionRule from "./ActionRule";
import StatusRule from "./StatusRule";

export default {
    components: {
        StatusRule,
        ActionRule
    },
    props: {
        actionType: Object,
        hideFooter: {
            type: Boolean,
            default: false
        },
        useRulesCollapse: {
            type: Boolean,
            default: true
        },
        hideHeader: {
            type: Boolean,
            default: false
        },
        hideDeleteButtons: {
            default: false,
            type: Boolean
        },
        showRules: {
            type: Boolean,
            default: true
        },
        editable: {
            type: Boolean,
            default: false
        },
        collapseRules: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            groupLabels: {
                'group_1': 'Gruppe 1',
                'group_2': 'Gruppe 2',
                'group_3': 'Gruppe 3',
                'group_4': 'Gruppe 4',
                'group_5': 'Gruppe 5',
            },
            data: {
                action_rules: [],
                category_id: '',
                description: '',
                full_width: false,
                id: '',
                image: '',
                inputs: [],
                instant: false,
                name: '',
                outputs: [],
                processors: [],
                reference: '',
                save_label: '',
                show_save_button: true,
                sort: null,
                status_rules: []
            },
            editableName: this.actionType.name,
            isCollapsed: false,
        };
    },
    computed: {
        ...mapGetters([
            'status_types',
            'simulation',
            'definition',
            'ui',
            'active_action_type_ids',
            'inaccessible_action_type_ids',
            'active_state_ids'
        ]),
        groupedActionRules() {
            let grouped = {};

            for (let i = 0; i < this.actionType.action_rules.length; i++) {
                let group = this.actionType.action_rules[i].group;

                if (grouped.hasOwnProperty(group)) {
                    grouped[group].push(this.actionType.action_rules[i]);
                }
                else {
                    grouped[group] = [this.actionType.action_rules[i]];
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
        ...utils, ...mapActions(reduxActions),
        statusType(statusTypeId) {
            for (let i = 0; i < this.status_types.length; i++) {
                if (this.status_types[i].id === statusTypeId) {
                    return this.status_types[i];
                }
            }

            return null;
        },
        state(statusType, value) {
            let floatVal = parseFloat(value);

            for (let i = 0; i < statusType.states.length; i++) {
                if (parseFloat(statusType.states[i].min) <= floatVal && parseFloat(statusType.states[i].max) >= floatVal) {
                    return statusType.states[i];
                }
            }

            return null;
        },
        operator(operatorString) {
            let operators = {
                'OPERATOR_EQUAL': 'ist gleich',
                'OPERATOR_NOT_EQUAL': 'ist nicht gleich',
                'OPERATOR_LOWER': 'ist kleiner als',
                'OPERATOR_LOWER_OR_EQUAL': 'ist kleiner oder gleich',
                'OPERATOR_GREATER_OR_EQUAL': 'ist größer oder gleich',
                'OPERATOR_GREATER': 'ist größer als',
                'OPERATOR_IN_ARRAY': 'ist entweder',
                'OPERATOR_IN_BETWEEN': 'ist zwischen',
            };

            return operators.hasOwnProperty(operatorString) ? operators[operatorString] : 'unbekannt';
        },
        openActionTypeModal(actionType) {
            this.openModal({
                componentName: 'ActionTypeModal',
                data: {
                    position: null,
                    actionTypeId: actionType.id
                }
            });
        },
        lastIndex(items, key) {
            return items.indexOf(key) === items.length - 1;
        },
        firstIndex(items, key) {
            return items.indexOf(key) === 0;
        },
        enableEditing(actionType) {
            this.editableName = actionType.name;
            this.$emit('setEditId', actionType.id);

            // So the focus is set after the redering of the input field is done
            this.$nextTick(() => {
                this.$refs[actionType.reference].focus();
            });
        },
        cancelEditing(name) {
            this.editableName = name;
            this.$emit('setEditId', null);
        },
        saveName() {
            if (this.$parent.editingId !== null) {
                if (this.actionType && this.editableName.trim() !== this.actionType.name && this.editableName.trim()) {
                    this.patchDefinition('UpdateActionType', {
                        ...this.actionType,
                        name: this.editableName.trim()
                    }).catch(() => {
                    });
                }
                this.$emit('setEditId', null);
            }
        },
        toggleContent() {
            this.isCollapsed = !this.isCollapsed;
        },
    }
};
</script>

<style scoped>
.actiontype-header:hover .quick-edit-btn {
    display: inline-block !important;
}
</style>