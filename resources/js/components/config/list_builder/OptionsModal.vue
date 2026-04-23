<template>
    <div>
        <div class="modal" id="genericModal" tabindex="-1" role="dialog" aria-labelledby="genericModal"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" v-if="column">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h5 class="modal-title">{{ column.label }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-2">
                        <div class="row d-flex" v-if="columnConfig.type">
                            <div class="col">
                                <DefaultOptions
                                    :column="columnConfig"
                                    :columns="columns"
                                    :column-labels="columnLabels"
                                    :alias-labels="aliasLabels"
                                    :used-column-aliases="usedColumnAliases"
                                    :editable="ui.editable"
                                    @type-change="onTypeChange"
                                    @property-change="onPropertyChange"
                                    @type-options-change="onTypeOptionsChange"
                                />
                                <hr/>
                                <component
                                    :is="optionsComponentName()"
                                    :column="columnConfig"
                                    :alias-labels="aliasLabels"
                                    :used-column-aliases="usedColumnAliases"
                                    :support-data="supportData"
                                    :editable="ui.editable"
                                    @property-change="onPropertyChange"
                                    @type-options-change="onTypeOptionsChange"
                                />
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="expand_width"
                                           :checked="columnConfig.expand_width" :disabled="!ui.editable"
                                           @change="onPropertyChange('expand_width', !columnConfig.expand_width)">
                                    <label class="custom-control-label" for="expand_width">Breite bei versteckten Spalten
                                        vergrößern.</label>
                                </div>
                                <hr/>
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="hide_sm" :checked="columnConfig.hide_sm"
                                           @change="onPropertyChange('hide_sm', !columnConfig.hide_sm)" :disabled="!ui.editable">
                                    <label class="custom-control-label" for="hide_sm">Auf Handy verstecken</label>
                                </div>
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="hide_md" :checked="columnConfig.hide_md"
                                           @change="onPropertyChange('hide_md', !columnConfig.hide_md)" :disabled="!ui.editable">
                                    <label class="custom-control-label" for="hide_md">Auf Tablet verstecken</label>
                                </div>
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="hide_lg" :checked="columnConfig.hide_lg"
                                           @change="onPropertyChange('hide_lg', !columnConfig.hide_lg)" :disabled="!ui.editable">
                                    <label class="custom-control-label" for="hide_lg">Auf Laptop verstecken</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 d-flex justify-content-end">
                        <div>
                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                                {{ ui.editable ? 'Abbrechen' : 'Schließen'}}
                            </button>
                            <button type="button" class="btn btn-sm btn-success" data-dismiss="modal" @click="onSave" v-if="ui.editable">
                                {{ addColumnMode ? 'Hinzufügen' : 'Speichern' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import DefaultOptions from "./columns/DefaultOptions";
import ButtonOptions from "./columns/ButtonOptions";
import IconOptions from "./columns/IconOptions";
import ProgressOptions from "./columns/ProgressOptions";
import TagsOptions from "./columns/TagsOptions";
import TextOptions from "./columns/TextOptions";
import CurrencyOptions from "./columns/CurrencyOptions.vue";
import UrlOptions from "./columns/UrlOptions";
import InputOptions from "./columns/InputOptions";
import SelectOptions from "./columns/SelectOptions";
import RowSelectionOptions from "./columns/RowSelectionOptions";
import ArrayDataOptions from "./columns/ArrayDataOptions";
import TemplateOptions from "./columns/TemplateOptions.vue";
import {defaultTypeOptions} from './columnTypes';
import {mapGetters} from "vuex";

export default {
    components: {
        DefaultOptions,
        ButtonOptions,
        IconOptions,
        ProgressOptions,
        TagsOptions,
        TextOptions,
        CurrencyOptions,
        UrlOptions,
        InputOptions,
        SelectOptions,
        RowSelectionOptions,
        ArrayDataOptions,
        TemplateOptions
    },
    props: {
        column: Object | null,
        columns: Array,
        addColumn: Function,
        updateColumn: Function,
        columnLabels: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        addColumnMode: Boolean,
        supportData: Object
    },
    data() {
        return {
            columnConfig: {},
            originalColumn: {},
            defaultTypeOptions: defaultTypeOptions
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
    },
    methods: {
        onSave() {
            if (this.addColumnMode) {
                let sort = this.columns.length ? (Math.max(...this.columns.map(ele => ele.sort)) + 1) : 0

                this.addColumn({
                    ...this.columnConfig,
                    sort: sort
                });
            }
            else {
                this.updateColumn(this.columnConfig);
            }

            this.$emit('close-modal');
        },
        optionsComponentName() {
            return this.columnConfig.type.charAt(0).toUpperCase() + this.columnConfig.type.slice(1) + 'Options';
        },
        onPropertyChange(property, value) {
            this.columnConfig = {
                ...this.columnConfig,
                [property]: value
            };
        },
        onTypeChange(type) {
            if (type === this.originalColumn.type) {
                this.columnConfig = this.originalColumn;
            }
            else {
                this.columnConfig = {
                    ...this.columnConfig,
                    type,
                    type_options: defaultTypeOptions[type] || {}
                };
            }
        },
        onTypeOptionsChange(property, value) {
            this.columnConfig = {
                ...this.columnConfig,
                type_options: {
                    ...this.columnConfig.type_options,
                    [property]: value
                }
            };
        }
    },
    created() {
        let that = this;
        $(document).one('hidden.bs.modal', '#genericModal', function () {
            that.$emit('close-modal');
        });
    },
    mounted() {
        $('#genericModal').modal('show');
        this.columnConfig = {...this.column};
        this.originalColumn = {...this.column};
    },
    beforeDestroy() {
        $('#genericModal').modal('hide');
    }
};
</script>
