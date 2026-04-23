<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="actionTypeSelection">Aktionen
                        <span class="badge badge-light">{{
                                actionTypeWhereIn.length ? actionTypeWhereIn.length : 'Alle'
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="actionTypeWhereIn = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="actionTypeSelection" v-model="actionTypeWhereIn" size="8">
                    <option v-for="actionType in action_types" :value="actionType.id">
                        {{ actionType.name }}
                    </option>
                </select>
                <small class="text-muted">Mehrere Optionen mit STRG/CMD gedrückt haltend markieren. Leer lassen für alle
                    Optionen.</small>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="selectSelection">Meta-Daten</label>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item py-1 pl-1"
                        v-for="item in [...supportData.select].sort((a, b) => a.alias > b.alias ? 1 : -1)">
                        <span v-if="aliasLabels.hasOwnProperty(item.alias)">{{ aliasLabels[item.alias] }} - </span>
                        <span>{{ item.alias }}</span>
                    </li>
                </ul>
                <small class="text-muted">Basis-Daten jeder Aktionsausführung.</small>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processData">Aktions-Daten
                        <span v-if="actionDataSelect.length" class="badge badge-light">{{
                                actionDataSelect.length
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="actionDataSelect = []"
                            v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="actionData" v-model="actionDataSelect" size="8"
                        :disabled="!ui.editable">
                    <option v-for="item in actionDataSelection" :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="statusData">Aktions-Statusveränderungen
                        <span v-if="statusDataSelect.length" class="badge badge-light">{{
                                statusDataSelect.length
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="statusDataSelect = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="statusData" v-model="statusDataSelect" size="8">
                    <option v-for="item in statusDataSelection" :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>
            <Where title="Filter" :data="data" :support-data="supportData" :used-column-aliases="usedColumnAliases"
                   :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
            <OrderBy :data="data" :support-data="supportData" :used-column-aliases="sortableColumnAliases"
                     :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
            <Casts :data="data" :support-data="supportData" :used-column-aliases="usedColumnAliases"
                   :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../../config-utils';
import {reduxActions} from '../../../../store/develop-and-config';
import Casts from "../partials/Casts.vue";
import Where from "../partials/Where.vue";
import OrderBy from "../partials/OrderBy.vue";

export default {
    components: {
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
        let actionTypeWhereIn = (whereIn.find(ele => ele[0].startsWith('actions')) || [])[1] || [];

        return {
            actionTypeWhereIn: actionTypeWhereIn,
            actionDataColumns: this.supportData.actionDataColumns,
            statusDataColumns: this.supportData.statusDataColumns,
            actionDataSelect: this.data.source.select.filter(ele => ele.startsWith('actions.action_data')),
            statusDataSelect: this.data.source.select.filter(ele => ele.startsWith('actions.status_data')),
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types'
        ]),
        actionTypeSelection() {
            let whereIn = (this.data.source.whereIn || [])[0] || [];

            return whereIn[1] || [];
        },
        actionDataSelectionOutputNames() {
            let outputNames = [];
            let actionTypes = this.actionTypeSelection.length ? this.action_types.filter(ele => this.actionTypeSelection.includes(ele.id)) : this.action_types;

            for (let i = 0; i < actionTypes.length; i++) {
                for (let j = 0; j < actionTypes[i].outputs.length; j++) {
                    if (!outputNames.includes(actionTypes[i].outputs[j].name)) {
                        outputNames.push(actionTypes[i].outputs[j].name);
                    }
                }
            }

            return outputNames;
        },
        actionDataSelection() {
            let items = this.actionDataColumns.filter(ele => this.actionDataSelectionOutputNames.includes(ele.column.split('->')[1]));

            for (let item of this.actionDataSelect) {
                let column = item.split(' as ')[0];
                let alias = item.split(' as ')[1];

                // Falls eine bereits selektierte Status-Spalte NICHT in der Support-Data ist, wird diese manuell
                // hinzugefügt, damit die Anzeige korrekt ist, auch wenn der Benutzer keinen Zugriff mehr
                // auf die Status hat.
                if (!items.find(ele => ele.column === column)) {
                    items.push({
                        label: alias.substring('actions_action_data_'.length) + ' (Datenfeld nicht in ausgewählten Aktionen vorhanden!)',
                        column: column,
                        alias: alias
                    });
                }
            }

            // Unique items by alias
            items = [
                ...new Map(items.map(item => [
                    item.alias,
                    item
                ])).values()
            ];

            return items.sort((a, b) => a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1);
        },
        statusDataSelection() {
            let items = this.statusDataColumns;

            for (let item of this.statusDataSelect) {
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
            items = [
                ...new Map(items.map(item => [
                    item.alias,
                    item
                ])).values()
            ];

            return items.sort((a, b) => a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1);
        },
        sortableColumnAliases() {
            let remove = [
                'processes_pipe_notation',
                'actions_id',
                'actions_action_type_id'
            ];

            return this.usedColumnAliases.filter(ele => !remove.includes(ele.alias));
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
    },
    watch: {
        actionTypeWhereIn() {
            let actionTypesWhereIn = this.actionTypeWhereIn.length ? [
                [
                    'actions.action_type_id',
                    this.actionTypeWhereIn
                ]
            ] : [];

            this.$emit('update-source', 'whereIn', [...actionTypesWhereIn]);
        },
        statusDataSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [
                ...new Set([
                    ...this.actionDataSelect,
                    ...this.statusDataSelect
                ])
            ];

            this.$emit('update-source', 'select', items);
        },
        actionDataSelect() {
            let items = [
                ...new Set([
                    ...this.actionDataSelect,
                    ...this.statusDataSelect
                ])
            ];

            this.$emit('update-source', 'select', items);
        }
    }
};
</script>

