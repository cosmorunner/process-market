<template>
    <div class="row mb-3">
        <div class="col-12">
                <span class="d-flex justify-content-between mb-2">
                    <span>
                        <span>{{ title }}</span>
                        <span class="badge badge-light"
                              v-if="(where.length + orWhere.length) > 0">{{ where.length + orWhere.length }}</span>
                    </span>
                    <button class="btn btn-sm btn-light" @click="toggleConnectingOperator"
                            v-if="(where.length + orWhere.length) > 0 && editable">
                        <span>
                            <span
                                :class="connectingBlockOperator === 'and' ? 'text-primary font-weight-bold' : ''">Und</span>/<span
                            :class="connectingBlockOperator === 'or' ? 'text-primary font-weight-bold' : ''">Oder</span> Block-Verknüpfung umkehren</span>
                    </button>
                </span>
            <!-- WHERE IN -->
            <template v-for="(item, index) in whereIn">
                <WhereInItem :editable="false" :where-in-item="item" :all-columns="supportData.allColumns"
                             :rule-index="index"/>
            </template>

            <span class="text-muted my-1 d-block pl-2" v-if="whereIn.length && (where.length || orWhere.length)">
                    <small>{{ connectingBlockOperator === 'and' ? 'und' : 'oder' }}</small>
                </span>

            <!-- WHERE -->
            <template v-for="(item, index) in where" v-if="where.length">
                <WhereItem :item="item" :editable="editable"
                           :connecting-operator="Array.isArray(item[0]) ? 'oder' : 'und'" :rule-index="index"
                           :used-column-aliases="usedColumnAliases" :all-columns="supportData.allColumns"
                           @delete-item="deleteItem"/>
                <span class="text-muted my-1 d-block pl-2" v-if="index < where.length - 1">
                        <small>und</small>
                    </span>
            </template>

            <!-- OR WHERE -->
            <template v-for="(item, index) in orWhere" v-if="orWhere.length">
                <WhereItem :item="item" :editable="editable"
                           :connecting-operator="Array.isArray(item[0]) ? 'und' : 'oder'"
                           :used-column-aliases="usedColumnAliases" :rule-index="index"
                           :all-columns="supportData.allColumns" @delete-item="deleteItem"/>
                <span class="text-muted my-1 d-block pl-2" v-if="index < orWhere.length - 1">
                        <small>oder</small>
                    </span>
            </template>

            <div class="input-group input-group-sm my-3 d-flex align-items-stretch" v-if="editable">
                <div class="input-group-prepend">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="whereOptions" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ newWhereType.label }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="whereOptions">
                        <button v-for="options in whereOptions" type="button" class="dropdown-item"
                                @click="newWhereType = options">{{ options.label }}
                        </button>
                    </div>
                </div>
                <div class="input-group-prepend">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" id="whereColumnAliases" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ labelByColumn(newItem[0]) || '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="whereColumnAliases">
                        <button type="button" class="dropdown-item" v-for="item in reducedColumnAliases"
                                @click="changeValue($event, 0, item.column)">
                            <span class="text-muted">Datenfeld: </span>{{ aliasLabels[item.alias] || item.alias }}
                        </button>
                        <button type="button" class="dropdown-item" v-for="item in []"
                                @click="changeValue($event, 0, item.value)"><span
                            class="text-muted">Syntax-Wert: </span> {{ item.label }}
                        </button>
                    </div>
                </div>
                <div class="input-group-prepend">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle border-right-0" type="button"
                            id="whereOperators" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ reducedOperatorLabels[newItem[1]] }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown" aria-labelledby="whereOperators">
                        <button type="button" v-for="(label, symbol) in reducedOperatorLabels" class="dropdown-item"
                                @click="changeValue($event, 1, symbol)">
                            {{ label }}
                        </button>
                    </div>
                </div>
                <input v-if="numericCastUsed" type="number" step="0.001" class="form-control" aria-label=""
                       :value="newItem[2]" @input="changeValue($event, 2)"/>
                <div class="input-group-append">
                    <DropdownSelector @selected="changeValue(null, 2, $event.value_with_label)" ref="dropdownSelector"
                                      :display-selected-value="true" :display-custom-selections="true"
                                      :rounded-borders="false" :icon="'edit'"
                                      :syntax-include="['process.meta', 'process.outputs', 'auth', 'users', 'reference.outputs', 'reference.meta', 'references.status', 'url']"/>
                </div>
                <div class="input-group-append">
                    <button class="btn btn-sm rounded-right btn-outline-success" @click="addItem">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from 'vuex';
import utils from "../../../../config-utils";
import WhereItem from "./WhereItem";
import WhereInItem from "./WhereInItem";
import DropdownSelector from "../../../utils/DropdownSelector";

export default {
    components: {
        DropdownSelector,
        WhereItem,
        WhereInItem
    },
    props: {
        supportData: Object | null,
        aliasLabels: Object,
        data: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean,
        title: {
            type: String,
            default: 'Konditionen'
        },
        allowedOperators: {
            type: Array,
            default: () => []
        },
        onlyColumnAliases: {
            type: Array,
            default: () => []
        },
        defaultColumn: {
            type: String,
            default: ''
        },
        defaultOperator: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            connectingBlockOperator: 'and',
            where: this.data.source.where || [],
            orWhere: this.data.source.orWhere || [],
            newWhereType: {
                index: 0,
                label: 'WHERE'
            },
            newItem: [
                '',
                '',
                ''
            ],
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
        ...mapGetters([
            'definition',
            'relation_types_with_single_process',
            'graphs_output_names',
            'graphs_status_types'
        ]),
        reducedOperatorLabels() {
            if (!this.allowedOperators.length) {
                return this.operatorLabels;
            }

            return this.allowedOperators.reduce((carry, item) => ({
                ...carry,
                [item]: this.operatorLabels[item]
            }), {});
        },
        reducedColumnAliases() {
            if (!this.onlyColumnAliases.length) {
                return this.usedColumnAliases;
            }

            return this.usedColumnAliases.filter((item) => this.onlyColumnAliases.includes(item.alias));
        },
        whereOptions() {
            if (!this.where.length && !this.orWhere.length) {
                return [
                    {
                        index: 0,
                        label: 'WHERE'
                    }
                ];
            }

            if (this.connectingBlockOperator === 'or') {
                return [
                    ...this.orWhere.map((ele, index) => ({
                        index: index,
                        label: (index + 1) + ': AND-WHERE hinzufügen'
                    })),
                    {
                        index: this.orWhere.length,
                        label: 'OR-WHERE Block hinzufügen'
                    }
                ];
            }

            if (this.connectingBlockOperator === 'and') {
                return [
                    ...this.where.map((ele, index) => ({
                        index: index,
                        label: (index + 1) + ': OR-WHERE hinzufügen'
                    })),
                    {
                        index: this.where.length,
                        label: 'AND-WHERE Block hinzufügen'
                    }
                ];
            }
        },
        numericCastUsed() {
            return this.newItem[0].includes('::numeric');
        },
        whereIn() {
            return this.data.source.whereIn || [];
        }
    },
    methods: {
        ...utils,
        changeValue(e, index, value) {
            let newItem = [...this.newItem];

            if (value === '' && e === null) {
                newItem[index] = '';
            }
            else {
                newItem[index] = value || e.target.value;
            }

            this.newItem = newItem;
        },
        addItem() {
            if (this.newItem[0] === '' || (this.newItem[0].includes('::numeric') && !this.isNumeric(this.newItem[2]))) {
                return;
            }

            let wheres = this.connectingBlockOperator === 'and' ? [...this.where] : [...this.orWhere];
            let newItem = [...this.newItem].map(ele => ele.trim());

            // Casting boolean and null values.
            if (newItem[2].startsWith('null|')) {
                newItem[2] = null;
            }
            else {
                if (newItem[2].startsWith('bool|')) {
                    newItem[2] = newItem[2].split('|')[1] === 'TRUE';
                }
            }

            // Neues where/orWhere hinzufügen
            if (typeof wheres[this.newWhereType.index] === 'undefined') {
                wheres = [
                    ...wheres,
                    newItem
                ];
            }
            // AND-WHERE zu einem orWhere hinzufügen indem die Regel bei dem index in ein Array mit der neuen Regel gepackt wird.
            else {
                let index = this.newWhereType.index;

                // Prüfen ob where/orWhere Item bereits genestet
                if (Array.isArray(wheres[index][0])) {
                    wheres[index] = [
                        ...wheres[index],
                        newItem
                    ];
                }
                else {
                    wheres[index] = [
                        wheres[index],
                        newItem
                    ];
                }
            }

            if (this.connectingBlockOperator === 'and') {
                this.where = [...wheres];
            }
            if (this.connectingBlockOperator === 'or') {
                this.orWhere = [...wheres];
            }

            // Reset
            this.resetNewItem();

            // Clear dropdown
            this.$refs.dropdownSelector.clearLastSelectedValue();

            this.newWhereType = {...this.whereOptions[0]};
        },
        labelByColumn(column) {
            let columnItem = this.supportData.allColumns.find(ele => ele.column === column);

            return columnItem ? columnItem.label : column;
        },
        toggleConnectingOperator() {
            if (this.connectingBlockOperator === 'or') {
                this.where = [...this.orWhere];
                this.orWhere = [];
            }
            else {
                this.orWhere = [...this.where];
                this.where = [];
            }

            this.connectingBlockOperator = this.connectingBlockOperator === 'or' ? 'and' : 'or';
            this.newWhereType = {...this.whereOptions[0]};
        },
        deleteItem(ruleIndex, arrayIndex = null) {
            let wheres = this.connectingBlockOperator === 'or' ? [...this.orWhere] : [...this.where];
            let rule = [...wheres[ruleIndex]];

            // Prüfen ob die gesamte Regel oder nur eine genestete Regel entfernt werden muss.
            if (Array.isArray(rule[0])) {
                rule = rule.filter((ele, index) => index !== arrayIndex);

                if (rule.length === 1) {
                    rule = [...rule[0]];
                }

                wheres = wheres.map((ele, index) => index !== ruleIndex ? ele : rule);
            }
            else {
                wheres = wheres.filter((ele, index) => index !== ruleIndex);
            }

            if (this.connectingBlockOperator === 'or') {
                this.orWhere = [...wheres];
            }
            else {
                this.where = [...wheres];
            }

            this.newWhereType = this.whereOptions[0];
        },
        isNumeric(str) {
            if (typeof str != "string") {
                return false;
            }
            return !isNaN(str) && !isNaN(parseFloat(str));
        },
        resetNewItem() {
            this.newItem = [
                this.defaultColumn,
                this.defaultOperator,
                ''
            ];
        }
    },
    watch: {
        where() {
            this.$emit('update-source', 'where', [...this.where]);
        },
        orWhere() {
            this.$emit('update-source', 'orWhere', [...this.orWhere]);
        }
    },
    mounted() {
        this.connectingBlockOperator = this.orWhere.length ? 'or' : 'and';
        this.newWhereType = {...this.whereOptions[0]};
        this.resetNewItem();
    }
};
</script>
