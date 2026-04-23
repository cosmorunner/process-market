<template>
    <div class="row">
        <div class="col-8">
            <div class="form-group mb-2">
                <label for="name" class="mb-0">Label</label>
                <input type="text" class="form-control form-control-sm" id="name" aria-describedby="name" name="name"
                       :readonly="!ui.editable" :value="listConfig.name" @change="updateMeta"/>
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="custom-control custom-switch mb-1">
                <input type="checkbox" class="custom-control-input" id="enableLabel" :disabled="!ui.editable"
                       :checked="enableLabel ? 'checked' : ''" @click="enableLabel = !enableLabel">
                <label class="custom-control-label" for="enableLabel">Label anzeigen</label>
            </div>
            <div class="form-group mb-2">
                <label for="description" class="mb-0">Beschreibung</label>
                <textarea class="form-control" id="description" rows="2" name="description" :readonly="!ui.editable"
                          :value="listConfig.description" @change="updateMeta"></textarea>
                <div v-for="error in (ui.validationErrors.description || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>

            <div class="form-group mb-2">
                <label for="slug" class="mb-0">Slug</label>
                <input type="text" class="form-control form-control-sm" id="slug" aria-describedby="label"
                       :readonly="!ui.editable" :value="listConfig.slug" @change="updateMeta" name="slug"/>
                <div v-for="error in (ui.validationErrors.slug || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <hr/>
            <div class="custom-control custom-switch mb-1">
                <input type="checkbox" class="custom-control-input" id="enableSearch" :disabled="!ui.editable"
                       :checked="enableSearch ? 'checked' : ''" @click="enableSearch = !enableSearch">
                <label class="custom-control-label" for="enableSearch">Suche anzeigen</label>
            </div>
            <div class="custom-control custom-switch mb-1">
                <input type="checkbox" class="custom-control-input" id="enablePagination" :disabled="!ui.editable"
                       :checked="enablePagination ? 'checked' : ''" @click="enablePagination = !enablePagination">
                <label class="custom-control-label" for="enablePagination">Paginierung anzeigen</label>
            </div>
            <div class="custom-control custom-switch mb-1">
                <input type="checkbox" class="custom-control-input" id="enableTotalCount" :disabled="!ui.editable"
                       :checked="enableTotalCount ? 'checked' : ''" @click="enableTotalCount = !enableTotalCount">
                <label class="custom-control-label" for="enableTotalCount">Gesamtergebnis anzeigen</label>
            </div>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="enableDownload" :disabled="!ui.editable"
                       :checked="enableDownload ? 'checked' : ''" @click="enableDownload = !enableDownload">
                <label class="custom-control-label" for="enableDownload">Download aktivieren</label>
            </div>
            <div class="form-group input-group-sm my-3">
                <label class="mb-0" for="rowsPerPage">Ergebnisse pro Seite</label>
                <select class="form-control" id="rowsPerPage" v-model="rowsPerPage" :disabled="!ui.editable">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                </select>
            </div>
            <hr/>
            <div v-if="listConfig.template !== 'connector_request'">
                <span>Schnell-Filter</span>
                <QuickFilter :listConfig="listConfig" :used-column-aliases="usedColumnAliases"
                             :alias-labels="aliasLabels" :quick-filter="quickFilter" :editable="ui.editable"
                             v-on="$listeners"/>
                <hr/>
            </div>
            <div v-if="listConfig.template !== 'connector_request'">
                <span>Filter</span>
                <Filters :listConfig="listConfig" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"
                         :filters="filters" :editable="ui.editable" v-on="$listeners"/>
                <hr/>
            </div>
            <div class="custom-control custom-switch mb-1">
                <input type="checkbox" class="custom-control-input" id="headerButton"
                       :checked="Object.keys(headerButton).length ? 'checked' : ''" @click="toggleHeaderButton"
                       :disabled="!ui.editable">
                <label class="custom-control-label" for="headerButton">Header-Button</label>
            </div>
            <div v-if="Object.keys(headerButton).length">
                <UrlOptions :type-options="headerButton.type_options" :alias-labels="aliasLabels"
                            :used-column-aliases="usedColumnAliases" :default-presets="getUrlDefaultPresets()"
                            :binding-value-labels="getUrlBindingValueLabels()" :preset="'#'" :show-label-helper="false"
                            :disable-placeholders="true" :editable="ui.editable" :conditions="headerButtonConditions"
                            @type-options-change="onHeaderButtonOptionsChange"
                            @header-conditions-change="onHeaderConditionsChange"/>

            </div>
        </div>
    </div>
</template>

<script>

import QuickFilter from "./partials/QuickFilter.vue";
import Filters from "./partials/Filters.vue";
import UrlOptions from "./columns/partials/UrlOptions";
import {mapGetters} from "vuex";

export default {
    components: {
        UrlOptions,
        QuickFilter,
        Filters
    },
    props: {
        listConfig: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object,
        supportData: Object | null
    },
    data() {
        return {
            rowsPerPage: this.listConfig.data.rows_per_page || 10,
            enableSearch: this.listConfig.data.enable_search,
            enableLabel: this.listConfig.data.enable_label || false,
            enablePagination: this.listConfig.data.enable_pagination,
            enableTotalCount: this.listConfig.data.enable_total_count,
            enableDownload: this.listConfig.data.enable_download || false,
            rolesEditTag: '',
            rolesEditAutocompleteItems: [],
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'ui'
        ]),
        quickFilter: function () {
            return this.listConfig.data.quick_filter;
        },
        filters() {
            return this.listConfig.data.filters || [];
        },
        headerButton() {
            return this.listConfig.data.header_button || {};
        },
        headerButtonConditions() {
            return this.headerButton.conditions || [];
        }
    },
    methods: {
        toggleHeaderButton() {
            if (Object.keys(this.headerButton).length) {
                this.$emit('update-data', 'header_button', []);
            }
            else {
                this.$emit('update-data', 'header_button', {
                    label: 'Neuer Prozess',
                    type: 'link',
                    type_options: {
                        url: '/processes/create',
                        bindings: [],
                        target: '_self'
                    },
                    conditions: []
                });
            }
        },
        onHeaderButtonOptionsChange(part, value) {
            let change = {...this.headerButton.type_options};
            if (typeof value !== 'undefined') {
                change[part] = value;
            }
            else {
                Object.keys(part).forEach(partKey => {
                    change[partKey] = part[partKey];
                });
            }

            this.$emit('update-data', 'header_button', {
                ...this.headerButton,
                type_options: change
            });
        },
        onHeaderConditionsChange(conditions) {
            this.$emit('update-data', 'header_button', {
                ...this.headerButton,
                conditions: conditions
            });
        },
        getUrlDefaultPresets() {
            let presets = {
                new_process: {
                    url: '/processes/create',
                    label: 'Neuer Prozess',
                    bindings: []
                }
            };

            let urls = Object.fromEntries(Object.entries(this.supportData.processUrls || {}).filter(([key]) => key.startsWith('initial-')));

            for (let item in urls) {
                presets[item] = {
                    url: urls[item].url,
                    label: urls[item].label,
                    bindings: urls[item].bindings
                };
            }

            return presets;
        },
        getUrlBindingValueLabels() {
            return {};
        },
        updateMeta(e) {
            this.$emit('update-meta', e.target.name, e.target.value);
        }
    },
    watch: {
        enableLabel(newValue) {
            this.$emit('update-data', 'enable_label', newValue);
        },
        enableSearch(newValue) {
            this.$emit('update-data', 'enable_search', newValue);
        },
        enablePagination(newValue) {
            this.$emit('update-data', 'enable_pagination', newValue);
        },
        enableDownload(newValue) {
            this.$emit('update-data', 'enable_download', newValue);
        },
        enableTotalCount(newValue) {
            this.$emit('update-data', 'enable_total_count', newValue);
        },
        rowsPerPage(newValue) {
            this.$emit('update-data', 'rows_per_page', newValue);
        }
    }
};
</script>
