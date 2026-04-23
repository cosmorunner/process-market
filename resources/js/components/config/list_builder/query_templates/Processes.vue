<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processTypeSelection">Prozesstypen
                        <span class="badge badge-light">{{processTypeWhereIn.length ? processTypeWhereIn.length : 'Alle'}}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="processTypeWhereIn = []" v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="processTypeSelection" v-model="processTypeWhereIn"
                        size="5" :disabled="!ui.editable">
                    <option v-for="fullNamespace in processTypeSelection" :value="fullNamespace">
                        {{ fullNamespace }}
                    </option>
                </select>
                <small class="text-muted">Mehrere Optionen mit STRG/CMD gedrückt haltend markieren. Leer lassen für alle Optionen.</small>
            </div>

            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="selectSelection">Meta-Daten
                        <span v-if="metaSelect.length" class="badge badge-light">{{ metaSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="metaSelect = []" v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="selectSelection" v-model="metaSelect" size="8" :disabled="!ui.editable">
                    <option v-for="item in coreTableColumns" :value="item.column + ' as ' + item.alias">
                        {{ item.label + ' - ' + item.alias }}
                    </option>
                </select>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processData">Prozess-Daten
                        <span v-if="outputSelect.length" class="badge badge-light">{{ outputSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="outputSelect = []" v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="processData" v-model="outputSelect" size="8"
                        :disabled="!ui.editable">
                    <option v-for="item in outputSelection"
                            :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="statusData">Status-Werte
                        <span v-if="statusSelect.length" class="badge badge-light">{{ statusSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="statusSelect = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="statusData" v-model="statusSelect" size="8">
                    <option v-for="item in statusTypeSelection"
                            :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>
            <Where title="Filter" :data="data" :support-data="supportData" :used-column-aliases="usedColumnAliases"
                   :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
            <OrderBy :data="data" :support-data="supportData" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
            <Casts :data="data" :support-data="supportData" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
        </div>
        <div class="col-12">
            <hr class="my-4">
            <div class="custom-control custom-switch mb-1">
                <input type="checkbox" class="custom-control-input" id="userInvolvement" :disabled="!ui.editable"
                       :checked="userInvolvement ? 'checked' : ''" @click="userInvolvement = !userInvolvement">
                <label class="custom-control-label" for="userInvolvement">Prozesse nach Benutzer-Zugriffen
                    filtern.</label>
            </div>
            <small class="text-muted">Nach Prozessen filtern, bei denen der Benutzer oder eine Gruppe des Benutzers eine
                Rolle innerhalb des Prozesses zugewiesen wurde.
                Implizite Zugriffe (z.B. durch öffentliche Rollen) werden nicht berücksichtigt.</small>
            <div v-if="userInvolvement">
                <div class="custom-control custom-switch pt-2">
                    <input type="checkbox" class="custom-control-input" id="userInvolvementAnyExecutable"
                           aria-label="Nach Prozessen filtern, bei denen der Benutzer eine beliebige oder festgelegte Aktion ausführen kann."
                           :checked="userInvolvementAnyExecutable ? 'checked' : ''"
                           @click="userInvolvementAnyExecutable = !userInvolvementAnyExecutable">
                    <label class="custom-control-label" for="userInvolvementAnyExecutable"></label>
                </div>
                <small class="text-muted">Nach Prozessen filtern, bei denen der Benutzer eine beliebige oder festgelegte
                    Aktion ausführen kann.</small>
            </div>
            <div v-if="userInvolvement" class="form-group mt-3">
                <hr>
                <RoleSelection :process-type-metas-roles-support-data="processRolesSupportData"
                               :process-type-metas-actions-support-data="processActionsSupportData"
                               :process-type-where-in="processTypeWhereIn"
                               :user-involvement-roles="userInvolvementRoles"
                               :with-actions="userInvolvementAnyExecutable"
                               @update-roles-selection="onUpdateRolesSelection"></RoleSelection>
            </div>
        </div>


    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../../config-utils';
import {reduxActions} from '../../../../store/develop-and-config';
import Where from "../partials/Where";
import OrderBy from "../partials/OrderBy";
import Casts from "../partials/Casts";
import RoleSelection from "../partials/RoleSelection.vue";

export default {
    components: {
        RoleSelection,
        OrderBy,
        Where,
        Casts
    },
    props: {
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        supportData: Object | null,
        data: Object
    },
    data() {
        // Bei ausgewählten Prozesstypen sind die Ids in dem ersten "whereIn" Statement am zweiten Index.
        // z.B. ['process_type_metas.full_namespace', ['allisa/inside', 'allisa/identity']]
        let whereIn = this.data.source.whereIn || [];

        return {
            userInvolvement: this.data.limit_to_user_involvement,
            userInvolvementAnyExecutable: this.data.limit_to_user_involvement_roles_any_executable,
            processTypeWhereIn: whereIn.length ? whereIn[0][1] : [],
            processesSupportData: this.supportData.processes,
            processRolesSupportData: this.supportData.processRoles,
            processActionsSupportData: this.supportData.processActions,
            coreTableColumns: this.supportData.coreTableColumns,
            statusTypeSupportData: this.supportData.statusTypes,
            outputSupportData: this.supportData.outputs,
            metaSelect: this.data.source.select.filter(ele => (ele.startsWith('processes') || ele.startsWith('process_type_metas') || ele.startsWith('process_types') || ele.startsWith('relations') || ele.endsWith('as processes_pipe_notation'))
                && (!ele.startsWith('processes.situation') && !ele.startsWith('processes.data') && !ele.startsWith('relations.data'))),
            statusSelect: this.data.source.select.filter(ele => ele.startsWith('processes.situation')),
            outputSelect: this.data.source.select.filter(ele => ele.startsWith('processes.data')),
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        userInvolvementRoles() {
            return this.data.limit_to_user_involvement_roles || [];
        },
        processTypeSelection() {
            return [
                ...new Set([
                    ...this.processTypeWhereIn,
                    ...this.processesSupportData.map(ele => ele.full_namespace)
                ])
            ].sort();
        },
        statusTypeSelection() {
            let items = [];

            if(!this.processTypeWhereIn.length) {
                items = Object.values(this.statusTypeSupportData).flat();
            } else {
                items = this.processTypeWhereIn.reduce((carry, item) => [...carry, ...this.statusTypeSupportData[item]], [])
            }

            for (let item of this.statusSelect) {
                let column = item.split(' as ')[0];
                let alias = item.split(' as ')[1];

                // Falls eine bereits selektierte Status-Spalte NICHT in der Support-Data ist, wird diese manuell
                // hinzugefügt, damit die Anzeige korrekt ist, auch wenn der Benutzer keinen Zugriff mehr
                // auf die Status hat.
                if (!items.find(ele => ele.column === column)) {
                    items.push({
                        label: '',
                        column: column,
                        alias: alias
                    });
                }
            }

            // Unique items by alias
            items = [...new Map(items.map(item => [item.alias, item])).values()];

            return items.sort((a, b) => a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1);
        },
        outputSelection(){
            let items = [];

            if(!this.processTypeWhereIn.length) {
                items = Object.values(this.outputSupportData).flat();
            } else {
                items = this.processTypeWhereIn.reduce((carry, item) => [...carry, ...(this.outputSupportData[item] || [])], [])
            }

            for(let item of this.outputSelect) {
                let column = item.split(' as ')[0]
                let alias = item.split(' as ')[1]

                // Falls eine bereits selektierte Status-Spalte NICHT in der Support-Data ist, wird diese manuell
                // hinzugefügt, damit die Anzeige korrekt ist, auch wenn der Benutzer keinen Zugriff mehr
                // auf die Status hat.
                if(!items.find(ele => ele.column === column)) {
                    items.push({
                        label: alias.substring('processes_data_'.length),
                        column: column,
                        alias: alias
                    })
                }
            }

            // Unique items by alias
            items = [...new Map(items.map(item => [item.alias, item])).values()];

            return items.sort((a, b) => a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onUpdateRolesSelection(roles) {
            this.$emit('update-data', 'limit_to_user_involvement_roles', roles);
        },
    },
    watch: {
        processTypeWhereIn() {
            this.statusSelect = [];
            this.outputSelect = [];

            let whereIn = this.processTypeWhereIn.length ? [
                [
                    'process_type_metas.full_namespace',
                    [... new Set(this.processTypeWhereIn)]
                ]
            ] : [];
            this.$emit('update-source', 'whereIn', whereIn);
        },
        metaSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [...new Set([...this.metaSelect,...this.statusSelect, ...this.outputSelect])]

            this.$emit('update-source', 'select', items);
        },
        statusSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [...new Set([...this.metaSelect,...this.statusSelect, ...this.outputSelect])]

            this.$emit('update-source', 'select', items);
        },
        outputSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [...new Set([...this.metaSelect,...this.statusSelect, ...this.outputSelect])]

            this.$emit('update-source', 'select', items);
        },
        userInvolvement(newValue) {
            this.$emit('update-data', 'limit_to_user_involvement', newValue);
        },
        userInvolvementAnyExecutable(newValue) {
            if (!newValue) {
            }
            this.$emit('update-data', 'limit_to_user_involvement_roles_any_executable', newValue && this.userInvolvement);
        }
    }
};
</script>

