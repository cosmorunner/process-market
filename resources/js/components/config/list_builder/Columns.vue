<!--suppress JSUnresolvedVariable -->
<template>
    <div class="row px-1 d-flex list-columns">
        <OptionsModal v-if="modal" :column="modal" :columns="columns" :update-column="updateColumn"
                      :add-column="addColumn" :column-labels="columnLabels" :alias-labels="aliasLabels"
                      :used-column-aliases="usedColumnAliases" :add-column-mode="addColumnMode"
                      :support-data="supportData" @close-modal="onCloseOptionsModal"/>
        <template v-for="(column, index) in sortedColumns">
            <div :class="'px-0 col-' + column.width">
                <div class="rounded-0 card h-100">
                    <div class="card-header px-2 py-1 d-flex justify-content-between border-primary">
                        <span class="text-muted text-truncate disable-user-select"><span
                            class="material-icons">{{ iconForType(column.type) }}</span> {{ column.label }}</span>
                        <span>
                            <span class="badge badge-light border" v-if="column.hide_sm || false">
                                <span class="material-icons text-danger">smartphone</span>
                            </span>
                            <span class="badge badge-light border" v-if="column.hide_md || false">
                                <span class="material-icons text-danger">tablet_android</span>
                            </span>
                             <span class="badge badge-light border" v-if="column.hide_lg || false">
                                <span class="material-icons text-danger">laptop_windows</span>
                            </span>
                            <button class="btn btn-sm btn-light text-danger p-0" @click="deleteColumn(index)"
                                    v-if="ui.editable">
                                <span class="material-icons">close</span>
                            </button>
                        </span>
                    </div>
                    <div class="card-body hover-pointer mouse-pointer p-2" @click="onOpenOptionsModal(column)">
                        <component :is="column.type.charAt(0).toUpperCase() + column.type.slice(1) + 'Column'"
                                   :column="column" :used-column-aliases="usedColumnAliases"
                                   :alias-labels="aliasLabels"/>
                    </div>
                    <div class="card-footer d-flex justify-content-between px-2 py-1">
                        <div class="text-nowrap">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm p-0 btn-light dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false" :disabled="!ui.editable">
                                    <small class="text-muted">{{ widthLabel(column.width) }}</small>
                                </button>
                                <div class="dropdown-menu">
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 1)">1 / 12
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 2)">1 / 6
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 3)">1 / 4
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 4)">1 / 3
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 5)">5 / 12
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 6)">1 / 2
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 7)">7 / 12
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 8)">2 / 3
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 9)">3 / 4
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 10)">10 / 12
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index, 11)">11 / 12
                                    </button>
                                    <button type="button" class="dropdown-item" @click="changeWidth(index,12)">1 / 1
                                    </button>
                                </div>
                            </div>
                            <span class="badge badge-light border" v-if="column.expand_width || false">
                                <span class="material-icons">settings_ethernet</span>
                            </span>
                        </div>
                        <div class="text-nowrap d-flex" v-if="ui.editable">
                            <button class="btn btn-sm btn-light p-0" @click="changeColumnSort(index, 'left')">
                                <span class="material-icons">arrow_left</span>
                            </button>
                            <button class="btn btn-sm btn-light p-0" @click="changeColumnSort(index, 'right')">
                                <span class="material-icons">arrow_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div v-if="displayAddColumnButton" :class="'px-0 col-' + (12 - totalColumnsWidth)">
            <div class="card p-2 h-100 rounded-0">
                <div class="card-body p-0" @click="onAddColumnModal">
                    <button class="btn btn-light btn-block h-100">
                        <span class="material-icons mi-2x">add</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import OptionsModal from "./OptionsModal";
import ButtonColumn from './columns/ButtonColumn';
import IconColumn from './columns/IconColumn';
import ProgressColumn from './columns/ProgressColumn';
import TagsColumn from './columns/TagsColumn';
import TextColumn from './columns/TextColumn';
import CurrencyColumn from "./columns/CurrencyColumn.vue";
import UrlColumn from './columns/UrlColumn';
import InputColumn from './columns/InputColumn';
import SelectColumn from './columns/SelectColumn';
import RowSelectionColumn from './columns/RowSelectionColumn';
import ArrayDataColumn from './columns/ArrayDataColumn';
import TemplateColumn from './columns/TemplateColumn.vue';
import {mapGetters} from "vuex";

export default {
    components: {
        OptionsModal,
        ButtonColumn,
        IconColumn,
        ProgressColumn,
        TagsColumn,
        TextColumn,
        CurrencyColumn,
        UrlColumn,
        InputColumn,
        SelectColumn,
        RowSelectionColumn,
        ArrayDataColumn,
        TemplateColumn
    },
    props: {
        listConfig: Object,
        supportData: Object | null,
        usedColumnAliases: Array | Object,
        aliasLabels: Object
    },
    data() {
        return {
            modal: null,
            addColumnMode: false,
            columnLabels: {
                button: 'Button',
                icon: 'Icon',
                progress: 'Fortschritt',
                tags: 'Tags',
                text: 'Text',
                currency: 'Währung',
                url: 'Link',
                input: 'Input',
                select: 'Auswahl',
                rowSelection: 'Zeilenauswahl',
                arrayData: 'JSON Liste',
                template: 'Vorlage'
            },
            defaultColumnOptions: {
                type: 'text',
                type_options: {},
                label: '',
                width: 1,
                searchable: false,
                sortable: false,
                color: null,
                hide: []
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        columns: function () {
            return [...this.listConfig.data.columns] || [];
        },
        sortedColumns: function () {
            return this.columns.sort((a, b) => (a.sort > b.sort) ? 1 : ((b.sort > a.sort) ? -1 : 0));
        },
        totalColumnsWidth: function () {
            return Object.keys(this.listConfig.data.columns).reduce((acc, columnName) => {
                return acc + this.listConfig.data.columns[columnName].width;
            }, 0);
        },
        displayAddColumnButton: function () {
            return this.totalColumnsWidth < 12;
        }
    },
    methods: {
        onOpenOptionsModal(columnOptions) {
            this.modal = columnOptions;
        },
        onAddColumnModal() {
            this.addColumnMode = true;
            this.modal = {
                ...this.defaultColumnOptions,
                width: 12 - this.totalColumnsWidth,
                sort: this.columns.length
            };
        },
        onCloseOptionsModal() {
            this.modal = null;
            this.addColumnMode = false;
        },
        addColumn(column) {
            let columns = [
                ...this.columns,
                column
            ];
            this.$emit('update-columns', columns);
        },
        updateColumn(column) {
            let columns = [...this.columns];

            columns = columns.map(ele => {
                if (ele.sort === column.sort) {
                    return column;
                }

                return ele;
            });

            this.$emit('update-columns', columns);
        },
        changeColumnSort(index, direction) {
            let columns = [...this.columns];
            let column = columns[index];
            let currentSort = column.sort;
            let maxSort = Math.max(...columns.map(ele => ele.sort));
            let targetSort = direction === 'left' ? currentSort - 1 : currentSort + 1;
            let indexOfSwappingElement = columns.findIndex(ele => ele.sort === targetSort);

            // Wenn bereits aussen und dann weiter nach aussen sortiert werden möchte
            if ((direction === 'left' && currentSort === 0) || (direction === 'right' && currentSort === maxSort)) {
                return;
            }

            // Spalte mit der Zielsortierung aktualisieren
            columns[indexOfSwappingElement] = {
                ...columns[indexOfSwappingElement],
                sort: currentSort
            };

            // Zu ändernde Spalte mit der neuen Sortierung aktualisieren
            columns[index] = {
                ...columns[index],
                sort: targetSort
            };

            this.$emit('update-columns', columns);
        },
        changeWidth(index, width) {
            let columns = [...this.columns];

            columns[index] = {
                ...columns[index],
                width: width
            };

            this.$emit('update-columns', columns);
        },
        iconForType(type) {
            let icons = {
                button: 'touch_app',
                icon: 'star',
                progress: 'flag',
                tags: 'local_offer',
                text: 'title',
                currency: 'euro_symbol',
                url: 'link',
                input: 'border_color',
                select: 'view_list',
                rowSelection: 'check_circle',
                arrayData: 'format_list_bulleted',
                template: 'wysiwyg'
            };

            return icons[type] || 'help';
        },
        deleteColumn(index) {
            let columns = [...this.columns];
            columns = [
                ...columns.slice(0, index),
                ...columns.slice(index + 1)
            ];
            this.$emit('update-columns', columns);
        },
        widthLabel(width) {
            let labels = {
                1: '1/12',
                2: '1/6',
                3: '1/4',
                4: '1/3',
                5: '5/12',
                6: '1/2',
                7: '7/12',
                8: '2/3',
                9: '3/4',
                10: '5/6',
                11: '11/12',
                12: '1/1'
            };

            return labels[width] || '?/?';
        },
    }
};
</script>

<style scoped>

.dropdown-toggle::after {
    content: none;
}

</style>
