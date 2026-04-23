<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processTypeSelection">Verknüpfungstypen
                        <span class="badge badge-light">{{
                                relationTypeWhereIn.length ? relationTypeWhereIn.length : 'Alle'
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="relationTypeWhereIn = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="relationTypeSelection" v-model="relationTypeWhereIn"
                        size="7">
                    <template v-for="item in relationTypeSelection">
                        <option :value="item.value">
                            {{ item.label }}
                        </option>
                    </template>
                </select>
                <small class="text-muted">Mehrere Optionen mit STRG/CMD gedrückt haltend markieren. Leer lassen für alle
                    Optionen.</small>
            </div>

            <div class="form-group mb-3" v-if="relationTypeWhereIn.length === 1">
                <div class="d-flex justify-content-between">
                    <label for="processData">Verknüpfungs-Daten
                        <span v-if="relationDataSelect.length" class="badge badge-light">{{
                                relationDataSelect.length
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="relationDataSelect = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="realtionsTypData" v-model="relationDataSelect" size="7">
                    <option v-for="(alias, column) in relationTypeDataSelection" :value="column + ' as ' + alias">
                        {{ alias.substr('relations_data_'.length) }} - {{ alias }}
                    </option>
                </select>
            </div>

            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processTypeSelection">Prozesstypen
                        <span class="badge badge-light">{{
                                processTypeWhereIn.length ? processTypeWhereIn.length : 'Alle'
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="processTypeWhereIn = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="processTypeSelection" v-model="processTypeWhereIn"
                        size="7">
                    <option v-for="fullNamespace in processTypeSelection" :value="fullNamespace">
                        {{ fullNamespace }}
                    </option>
                </select>
            </div>

            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="selectSelection">Meta-Daten
                        <span v-if="metaSelect.length" class="badge badge-light">{{ metaSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="metaSelect = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="selectSelection" v-model="metaSelect" size="8">
                    <option v-for="item in coreTableColumns" :value="item.column + ' as ' + item.alias">
                        {{ item.label + ' - ' + item.alias }}
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
                    <option v-for="item in statusTypeSelection" :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>

            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processData">Prozess-Daten
                        <span v-if="outputSelect.length" class="badge badge-light">{{ outputSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="outputSelect = []">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="processData" v-model="outputSelect" size="8">
                    <option v-for="item in outputSelection" :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>

            <OrderBy :data="data" :support-data="supportData" :used-column-aliases="usedColumnAliases"
                     :alias-labels="aliasLabels" v-on="$listeners"/>
        </div>

        <div class="col-12">
            <hr class="my-4">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="userInvolvement"
                       :checked="userInvolvement ? 'checked' : ''" @click="userInvolvement = !userInvolvement">
                <label class="custom-control-label" for="userInvolvement"></label>
            </div>
            <small class="text-muted">Nach Prozessen filtern, bei denen der Benutzer eine Rolle einnimmt.</small>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../../config-utils';
import {reduxActions} from '../../../../store/develop-and-config';
import OrderBy from "../partials/OrderBy";

export default {
    components: {OrderBy},
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
        let relationTypeWhereIn = (whereIn.find(ele => ele[0].startsWith('relations')) || [])[1] || [];
        let processTypeWhereIn = (whereIn.find(ele => ele[0].startsWith('process_type_metas')) || [])[1] || [];

        return {
            userInvolvement: this.data.limit_to_user_involvement,
            relationTypeWhereIn: relationTypeWhereIn,
            processTypeWhereIn: processTypeWhereIn,
            relationTypeDataSupportData: this.supportData.relationTypeData,
            relationTypesPipeNotation: this.supportData.relationTypesPipeNotation,
            processesSupportData: this.supportData.processes,
            coreTableColumns: this.supportData.coreTableColumns,
            statusTypeSupportData: this.supportData.statusTypes,
            outputSupportData: this.supportData.outputs,
            metaSelect: this.data.source.select.filter(ele => (ele.startsWith('processes') || ele.startsWith('process_type_metas') || ele.startsWith('process_types') || ele.startsWith('relations') || ele.endsWith('as processes_pipe_notation')) && (!ele.startsWith('processes.situation') && !ele.startsWith('processes.data') && !ele.startsWith('relations.data'))),
            statusSelect: this.data.source.select.filter(ele => ele.startsWith('processes.situation')),
            outputSelect: this.data.source.select.filter(ele => ele.startsWith('processes.data')),
            relationDataSelect: this.data.source.select.filter(ele => ele.startsWith('relations.data')),
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'relation_types',
            'definition',
            'graphs'
        ]),
        context() {
            return this.data.source.where[0][2] || '';
        },
        relationTypeSelection() {
            let items = [];
            let pipeNotations = Object.values(this.relationTypesPipeNotation).flat();

            // Die aktuelle Auswahl hinzufügen für den Fall, dass der Benutzer keinen Zugriff mehr auf die Prozesse hat.
            let that = this;
            this.relationTypeWhereIn.forEach(function (whereIn) {
                // For internal relation types, the saved syntax loader is removed before comparison.
                if (whereIn.startsWith('[[process.process_type.full_namespace]]::')) {
                    whereIn = whereIn.replace('[[process.process_type.full_namespace]]::', '');
                }
                let found = pipeNotations.filter(function (pipeNotation) {
                    let parts = that.getSyntaxParts(pipeNotation);
                    return parts.syntax === whereIn;
                });
                if (!found.length) {
                    pipeNotations.push(whereIn);
                }
            });

            pipeNotations.sort().forEach(function (pipeNotation) {
                let parts = that.getSyntaxParts(pipeNotation);
                let label = parts.label;
                if (!label) {
                    label = (parts.namespace ? (parts.namespace + ' - ') : '') + parts.key;
                }

                let value = parts.syntax;
                if (!parts.namespace) {
                    // For internal relation types, a syntax loader is added as namespace to the pipe notation.
                    value = '[[process.process_type.full_namespace]]::' + value;
                }
                items.push({
                    label: label,
                    value: value
                });
            });

            return items;
        },
        relationTypeDataSelection() {
            if (!this.relationTypeWhereIn.length) {
                return [];
            }
            let selectedRelationTypeSyntax = this.relationTypeWhereIn[0] || '';
            // Aus Pipe-Loader Syntax holen, z.B. allisa/demo@1.0.0::relationType|98ea8685-a795-452a-b81e-6c45c24ab831
            let selectedRelationTypeId = (selectedRelationTypeSyntax.split('|')[1] || '').split('[')[0] || '';
            let selectedRelationTypeNamespace = this.definition.namespace + '/' + this.definition.name;
            if (selectedRelationTypeSyntax.includes('::')) {
                selectedRelationTypeNamespace = selectedRelationTypeSyntax.split('::')[0];
            }
            let relationData = {};

            // Aktuelle hinzufügen, damit die Anzeige korrekt ist, auch wenn der Benutzer keinen Zugriff mehr auf die
            // Prozesse hat.
            for (let item of this.relationDataSelect) {
                let alias = item.split(' as ')[1];
                let column = item.split(' as ')[0];
                relationData[column] = alias;
            }

            for (let fullNamespace in this.relationTypeDataSupportData) {
                for (let relationTypeId in this.relationTypeDataSupportData[fullNamespace]) {
                    if (selectedRelationTypeId === relationTypeId && selectedRelationTypeNamespace === fullNamespace) {
                        for (const item of this.relationTypeDataSupportData[fullNamespace][relationTypeId] || []) {
                            relationData[item.column] = item.alias;
                        }
                    }
                }
            }

            return relationData;
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
        outputSelection() {
            let items = [];

            if(!this.processTypeWhereIn.length) {
                items = Object.values(this.outputSupportData).flat();
            } else {
                items = this.processTypeWhereIn.reduce((carry, item) => [...carry, ...(this.outputSupportData[item] || [])], [])
            }

            for (let item of this.outputSelect) {
                let column = item.split(' as ')[0];
                let alias = item.split(' as ')[1];

                // Falls eine bereits selektierte Status-Spalte NICHT in der Support-Data ist, wird diese manuell
                // hinzugefügt, damit die Anzeige korrekt ist, auch wenn der Benutzer keinen Zugriff mehr
                // auf die Status hat.
                if (!items.find(ele => ele.column === column)) {
                    items.push({
                        label: alias.substr('processes_data_'.length),
                        column: column,
                        alias: alias
                    });
                }
            }

            // Unique items by alias
            items = [...new Map(items.map(item => [item.alias, item])).values()];

            return items.sort((a, b) => a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        updateWhereIn() {
            let processTypeWhereIn = this.processTypeWhereIn.length ? [
                [
                    'process_type_metas.full_namespace',
                    [...new Set(this.processTypeWhereIn)]
                ]
            ] : [];

            let relationTypeWhereIn = this.relationTypeWhereIn.length ? [
                [
                    'relations.relation_type_pipe_notation',
                    [...new Set(this.relationTypeWhereIn)]
                ]
            ] : [];

            let whereIns = [
                ...processTypeWhereIn,
                ...relationTypeWhereIn
            ];

            this.$emit('update-source', 'whereIn', whereIns);
        }
    },
    watch: {
        processTypeWhereIn() {
            this.statusSelect = [];
            this.outputSelect = [];

            // Sowohl bei einer Änderung der Prozesstypen als auch bei einer Änderung der Verknüpfungstypen
            // muss das Where-In aktualisiert werden.
            this.updateWhereIn();
        },
        relationTypeWhereIn() {
            this.statusSelect = [];
            this.outputSelect = [];
            this.relationDataSelect = [];

            // Sowohl bei einer Änderung der Prozesstypen als auch bei einer Änderung der Verknüpfungstypen
            // muss das Where-In aktualisiert werden.
            this.updateWhereIn();
        },
        metaSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [
                ...new Set([
                    ...this.metaSelect,
                    ...this.statusSelect,
                    ...this.outputSelect,
                    ...this.relationDataSelect
                ])
            ];

            this.$emit('update-source', 'select', items);

        },
        statusSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [
                ...new Set([
                    ...this.metaSelect,
                    ...this.statusSelect,
                    ...this.outputSelect,
                    ...this.relationDataSelect
                ])
            ];

            this.$emit('update-source', 'select', items);
        },
        outputSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [
                ...new Set([
                    ...this.metaSelect,
                    ...this.statusSelect,
                    ...this.outputSelect,
                    ...this.relationDataSelect
                ])
            ];

            this.$emit('update-source', 'select', items);
        },
        relationDataSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [
                ...new Set([
                    ...this.metaSelect,
                    ...this.statusSelect,
                    ...this.outputSelect,
                    ...this.relationDataSelect
                ])
            ];

            this.$emit('update-source', 'select', items);
        },
        userInvolvement(newValue) {
            this.$emit('update-data', 'limit_to_user_involvement', newValue);
        }
    }
};
</script>

