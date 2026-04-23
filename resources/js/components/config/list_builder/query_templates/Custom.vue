<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label for="from">From - Tabelle</label>
                    </div>
                    <select class="form-control form-control-sm" id="from" v-model="from">
                        <option v-for="table in tables" :value="table.name">
                            {{ table.label }} - {{ table.name }}
                        </option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label for="from">Select - Datenfelder</label>
                    </div>
                    <table class="table table-sm" v-if="select.length">
                        <thead>
                        <tr>
                            <th class="border-0 font-weight-normal text-muted"><small>Datenfeld</small></th>
                            <th class="border-0 font-weight-normal text-muted"><small>Alias</small></th>
                            <th class="border-0 font-weight-normal"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in select">
                            <td>{{ item.split(' as ')[0] }}</td>
                            <td>{{ item.split(' as ')[1] }}</td>
                            <td>
                                <button class="btn btn-sm btn-light float-right" @click="deleteSelect(item)">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <span class="material-icons">list</span>
                            </button>
                            <div class="dropdown-menu scrollable-dropdown">
                                <button class="dropdown-item" type="button" v-for="column in allColumns"
                                        @click="fillCoreColumn(column.column, column.alias)">{{ column.label }}
                                </button>
                            </div>
                        </div>
                        <input type="text" class="form-control" placeholder="Datenfeld..." v-model="newSelect.column"
                               aria-label="Datenfeld">
                        <input type="text" class="form-control" placeholder="Alias..." v-model="newSelect.alias"
                               aria-label="Alias">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-success" @click="addSelect">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <label>Inner Join - Statements</label>
                    </div>
                    <div class="form-group input-group-sm">
                        <CodeEditor :code="JSON.stringify(join, null, 4)" @blur="updateJoin"/>
                        <small :class="(ui.loading ? 'text-success' : 'text-muted')" v-if="validJson.join">
                            <span class="material-icons">done</span>
                            <span>Gespeichert</span>
                        </small>
                        <small class="text-danger" v-else>
                            <span class="material-icons">close</span>
                            <span>Kein Array - Nicht gespeichert.</span>
                        </small>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <label>Left Join - Statements</label>
                    </div>
                    <div class="form-group input-group-sm">
                        <CodeEditor :code="JSON.stringify(leftJoin, null, 4)" @blur="updateLeftJoin"/>
                        <small :class="(ui.loading ? 'text-success' : 'text-muted')" v-if="validJson.leftJoin">
                            <span class="material-icons">done</span>
                            <span>Gespeichert</span>
                        </small>
                        <small class="text-danger" v-else>
                            <span class="material-icons">close</span>
                            <span>Kein Array - Nicht gespeichert.</span>
                        </small>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <label>Where - Konditionen</label>
                    </div>
                    <div class="form-group input-group-sm">
                        <CodeEditor :code="JSON.stringify(where, null, 4)" @blur="updateWhere"/>
                        <small :class="(ui.loading ? 'text-success' : 'text-muted')" v-if="validJson.where">
                            <span class="material-icons">done</span>
                            <span>Gespeichert</span>
                        </small>
                        <small class="text-danger" v-else>
                            <span class="material-icons">close</span>
                            <span>Kein Array - Nicht gespeichert.</span>
                        </small>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <label>OrderBy - Statements</label>
                    </div>
                    <div class="form-group input-group-sm">
                        <CodeEditor :code="JSON.stringify(orderBy, null, 4)" @blur="updateOrderBy"/>
                        <small :class="(ui.loading ? 'text-success' : 'text-muted')" v-if="validJson.orderBy">
                            <span class="material-icons">done</span>
                            <span>Gespeichert</span>
                        </small>
                        <small class="text-danger" v-else>
                            <span class="material-icons">close</span>
                            <span>Kein Array - Nicht gespeichert.</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../../config-utils';
import {reduxActions} from '../../../../store/develop-and-config';
import CodeEditor from "../../CodeEditor";

export default {
    components: {CodeEditor},
    props: {
        supportData: Object | null,
        data: Object,
        usedColumnAliases: Array | Object
    },
    data() {
        return {
            tables: this.supportData.tables || [],
            allColumns: this.supportData.allColumns,

            from: this.data.source.from,
            select: this.data.source.select || [],
            join: this.data.source.join || [],
            leftJoin: this.data.source.leftJoin || [],
            where: this.data.source.where || [],
            orderBy: this.data.source.orderBy || [],

            validJson: {
                where: true,
                join: true,
                leftJoin: true,
                orderBy: true
            },
            newSelect: {
                column: '',
                alias: ''
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        fillCoreColumn(column, alias) {
            this.newSelect.column = column;
            this.newSelect.alias = alias;
        },
        addSelect() {
            if (this.newSelect.column.trim() === ''
                || this.newSelect.alias.trim() === ''
                || this.usedColumnAliases.map(ele => ele.alias).includes(this.newSelect.alias.trim())) {
                return;
            }

            this.select = [
                ...this.select,
                this.newSelect.column.trim() + ' as ' + this.newSelect.alias.trim()
            ];
            this.newSelect = {
                column: '',
                alias: ''
            };
        },
        deleteSelect(item) {
            let select = [...this.select];

            this.select = select.filter(ele => ele !== item);
        },
        updateWhere(newVal) {
            this.updateJsonPart(newVal, 'where');
        },
        updateJoin(newVal) {
            this.updateJsonPart(newVal, 'join');
        },
        updateLeftJoin(newVal) {
            this.updateJsonPart(newVal, 'leftJoin');
        },
        updateOrderBy(newVal) {
            this.updateJsonPart(newVal, 'orderBy');
        },
        updateJsonPart(newVal, part) {
            this.setValidJson(part, true);

            let arr = false;
            let value = newVal.trim() === '' ? '[]' : newVal.trim();
            let cleaned = value.replace(/\s+/g, '');

            try {
                arr = JSON.parse(cleaned);
            } catch (e) {
                this.setValidJson(part, false);

                return;
            }

            if (arr && typeof Array.isArray(arr)) {
                this.setValidJson(part, true);

                if (cleaned === JSON.stringify(this[part] || []).trim()) {
                    return;
                }

                // Neuen Wert setzen. Der Watcher speichert ab.
                this[part] = arr;

                return;
            }

            this.setValidJson(part, false);
        },
        setValidJson(part, booleanVal) {
            this.validJson = {
                ...this.validJson,
                [part]: booleanVal
            };
        }
    },
    watch: {
        from() {
            this.$emit('update-source', 'from', this.from);
        },
        select(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }
            this.$emit('update-source', 'select', [...this.select]);
        },
        join() {
            this.$emit('update-source', 'join', [...this.join]);
        },
        leftJoin() {
            this.$emit('update-source', 'leftJoin', [...this.leftJoin]);
        },
        where() {
            this.$emit('update-source', 'where', [...this.where]);
        },
        orderBy() {
            this.$emit('update-source', 'orderBy', [...this.orderBy]);
        }
    }
};
</script>

