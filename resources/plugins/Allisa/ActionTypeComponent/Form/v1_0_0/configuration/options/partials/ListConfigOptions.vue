<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Optionen aus Listenkonfiguration</label>
            <select class="form-control" v-model="config.id" @change="onListConfigChange" :disabled="!editable">
                <option value="">Keine Listenkonfiguration</option>
                <option :value="listConfig.id" v-for="listConfig in list_configs">
                    {{ listConfig.name + ' - ' + listConfig.slug }}
                </option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-1" v-if="selectedListConfig && enableLabelFieldAlias">
            <label class="mb-0" for="labelAlias">Label</label>

            <Concatenation :editable="editable" :concat="concat" :selectable-aliases="listConfigColumnAliases"
                           @concat-updated="onConcatUpdated"/>

            <small class="text-muted">Wählen Sie ein Datenfeld, welches für das Options-Label genutzt wird.</small>
        </div>
        <div class="form-group input-group-sm mb-1" v-if="selectedListConfig && enableValueFieldAlias">
            <label class="mb-0" for="valueAlias">Wert</label>
            <select class="form-control" id="valueAlias" v-model="config.value_field_alias">
                <option :value="alias" v-for="alias in listConfigColumnAliases">{{ alias }}</option>
            </select>
            <small class="text-muted">Wählen Sie ein Datenfeld, welches für den Options-Wert genutzt wird.</small>
        </div>
        <div class="form-group input-group-sm mb-1" v-if="selectedListConfig && enableSearchOption">
            <label class="mb-0" for="searchField">Suche</label>
            <select class="form-control" id="searchField" v-model="config.search">
                <option value="">Keine Listensuche</option>
                <option :value="alias" v-for="alias in inputOutputNames">{{ alias }}</option>
            </select>
            <small class="text-muted">Wählen Sie ein Datenfeld, welches für die Listen-Suche genutzt wird.
                Datenfeld-Wert muss eine Zeichenkette sein.</small>
        </div>
        <div class="mb-3" v-if="selectedListConfig">
            <span class="d-block">Filter</span>
            <ListFilters :list-config-filters="selectedListConfig.data.filters || []"
                         :current-filters="config.filters || []" :input-output-names="inputOutputNames"
                         @update-filters="onUpdateFilters" :editable="editable"/>
            <small class="text-muted">Wenden Sie Filter für die Liste an.</small>
        </div>
        <div class="mb-3" v-if="selectedListConfig">
            <span class="d-block">URL Query-Params</span>
            <QueryParams :list-config-filters="selectedListConfig.data.filters || []"
                         :query-params="config.query_params || {}" :input-output-names="inputOutputNames"
                         @update-params="onUpdateParams" :editable="editable"/>
        </div>
        <div class="mb-3" v-if="selectedListConfig">
            <span class="d-block"></span>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="contextParameterSwitch"
                       @click="toggleApplyContextParameter" :checked="config.apply_context_parameter"
                       :disabled="!editable">
                <label class="custom-control-label" for="contextParameterSwitch">"context"-URL-Parameter
                    übernehmen</label>
                <small class="text-muted d-block">Den aktuellen "context"-URL-Parameter aus der URL übernehmen.</small>
            </div>
        </div>

    </div>
</template>

<script>

import {mapGetters} from 'vuex';
import utils from "../../../../../../../../js/config-utils";
import ListFilters from "./ListFilters.vue";
import QueryParams from "./QueryParams.vue";
import Concatenation from "./Concatenation.vue";

export default {
    components: {
        Concatenation,
        QueryParams,
        ListFilters
    },
    props: {
        field: Object,
        inputOutputNames: Array,
        editable: Boolean,
        enableSearchOption: {
            type: Boolean,
            default: true
        },
        enableLabelFieldAlias: {
            type: Boolean,
            default: true
        },
        enableValueFieldAlias: {
            type: Boolean,
            default: true
        }
    },
    data() {
        let config = this.field.list_config || {};

        return {
            config: {...config},
            listConfigColumnAliases: []
        };
    },
    computed: {
        ...mapGetters([
            'list_configs',
            'ui'
        ]),
        concat: function () {
            if (this.config.label_field_alias === undefined) {
                return [
                    '',
                    '',
                    ''
                ];
            }
            if (typeof this.config.label_field_alias === 'string') {
                return [
                    '',
                    this.config.label_field_alias,
                    ''
                ];
            }

            return this.config.label_field_alias;
        },
        selectedListConfig: function () {
            if (this.config) {
                return this.list_configs.find(ele => ele.id === this.config.id);
            }

            return null;
        }
    },
    methods: {
        ...utils,
        onListConfigChange(e) {
            if (e.target.value === '') {
                this.config = {
                    id: ''
                };

                this.$emit('updated-list-config-column-aliases', []);
            }
            else {
                this.config = {
                    id: e.target.value,
                    filters: [],
                    query_params: {}
                };

                this.loadSelectItems();

                if (this.enableSearchOption) {
                    this.config['search'] = '';
                }
            }
        },
        onUpdateFilters(filters) {
            this.config = {
                ...this.config,
                filters: filters
            };
        },
        onUpdateParams(params) {
            this.config = {
                ...this.config,
                query_params: params
            };
        },
        toggleApplyContextParameter() {
            this.config = {
                ...this.config,
                apply_context_parameter: !this.config.apply_context_parameter
            };
        },
        onConcatUpdated(concat) {
            this.config = {
                ...this.config,
                label_field_alias: concat
            };
        },
        loadSelectItems() {
            let that = this;

            that.listConfigColumnAliases = [];

            if (this.selectedListConfig) {
                axios.get(this.ui.urls.list_support.replace('-listConfigId-', this.selectedListConfig.id) + '?parts=select').then(function (response) {
                    that.listConfigColumnAliases = response.data.select.map(ele => ele.alias || '').filter(ele => ele !== '');
                    that.$emit('updated-list-config-column-aliases', that.listConfigColumnAliases);
                }).catch(function (error) {
                    that.loading = false;
                    console.log(error);
                });
            }
        }
    },
    watch: {
        config: {
            handler: function (newVal) {
                this.$emit('property-change', 'list_config', newVal);
            },
            deep: true
        }
    },
    mounted() {
        this.loadSelectItems();
    }
};
</script>
