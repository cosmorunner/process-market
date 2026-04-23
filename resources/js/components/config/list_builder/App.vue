<template>
    <div>
        <LoadingSeparator :clear-error="clearError" :ui="ui"/>
        <div v-if="detailComponentName !== 'New' && listConfig" class="row mb-2">
            <div class="col-8">
                <div class="d-flex justify-content-between">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Options' ? 'active ' : '')"
                               href="#" @click="onUpdateSubNavigation('Options')">Optionen</a>
                        </li>
                        <li class="nav-item">
                            <a :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Query' ? 'active ' : '')"
                               href="#" @click="onUpdateSubNavigation('Query')">Daten</a>
                        </li>
                        <li class="nav-item">
                            <a :class="'nav-link px-2 py-1 ' + (detailComponentName === 'Columns' ? 'active ' : '')"
                               href="#" @click="onUpdateSubNavigation('Columns')">Spalten</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div v-if="listConfig" class="col-4 d-flex justify-content-end">
                <div v-if="ui.editable && showPasteListButton" aria-label="Button group with nested dropdown"
                     class="btn-group" role="group">
                    <button class="btn btn-sm btn-warning text-nowrap" @click="pasteList">
                        Einfügen
                    </button>
                    <button class="btn btn-sm btn-warning" @click="clearCopyElement">
                        <span class="material-icons">close</span>
                    </button>
                </div>
                <button class="btn btn-sm text-muted p-0 ml-2" @click="copyList">
                    <span class="material-icons">content_copy</span>
                </button>
                <button v-if="ui.editable" class="btn btn-sm text-muted mx-3" @click="onDeleteListConfig">
                    <span class="material-icons">delete</span>
                </button>
                <select id="action_type_id" :value="listConfig.id" class="form-control form-control-sm mr-3"
                        @change="onChangeListConfig">
                    <option v-for="listConfig in definition.list_configs" :value="listConfig.id">
                        {{ listConfig.name + ' - ' + listConfig.slug }}
                    </option>
                </select>
                <button v-if="ui.editable" class="btn btn-sm btn-primary text-nowrap mr-2"
                        @click="detailComponentName = 'New'">
                    Neue Liste
                </button>
                <Docs class="mr-2" article="config-lists"/>
            </div>
        </div>
        <button v-if="!listConfig && detailComponentName !== 'New'" class="btn btn-primary btn-sm mb-2"
                @click="detailComponentName = 'New'">Liste anlegen
        </button>
        <div v-if="detailComponentName === 'New'" class="row">
            <div class="col">
                <div class="bg-white border border-0 p-1">
                    <component :is="detailComponentName" @update-sub-navigation="onUpdateSubNavigation"/>
                </div>
            </div>
        </div>

        <div v-if="listConfig && supportData && supportFetched && !(detailComponentName === 'New')" class="row">
            <div class="col">
                <div class="bg-white border border-0 p-1">
                    <component :is="detailComponentName" :alias-labels="aliasLabels" :list-config="listConfig"
                               :support-data="supportData" :used-column-aliases="usedColumnAliases" v-on="$listeners"
                               @update-source="onUpdateSource" @update-multiple-source="onUpdateMultipleSource"
                               @update-columns="onUpdateColumns" @update-data="onUpdateData" @update-meta="onUpdateMeta"
                               @update-sub-navigation="onUpdateSubNavigation"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../config-utils';
import {reduxActions} from '../../../store/develop-and-config';
import Query from "./Query";
import Columns from "./Columns";
import Options from "./Options";
import SaveButton from "./SaveButton";
import New from "./New";
import LoadingSeparator from "../LoadingSeparator";
import Docs from "../../utils/Docs.vue";

export default {
    components: {
        Docs,
        LoadingSeparator,
        SaveButton,
        Query,
        Columns,
        Options,
        New
    },
    data() {
        return {
            detailComponentName: 'Options',
            supportData: null,
            supportFetched: false,
            listConfig: null,
            components: [
                'Options',
                'Query',
                'Options',
                'New',
                'Columns'
            ]
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'list_configs',
            'ui'
        ]),
        usedColumnAliases: function () {
            // We fall back to the "supportData.allColumns", because in some list template the "select" items
            // do not exist, as they are statically set by the list builder in the allisa platform (no need to have them in list config).
            // When no "select" is provided by the listconfig, the "allColumns"

            // Some list template do not have a "select" propety, because the select is statically set by the list builder in the Allisa platform.
            // For example in the "Benutzer" and "Gruppen" process lists, the user can not select any data.
            let select;

            if (!this.listConfig.data.source.hasOwnProperty('select') && this.supportData.hasOwnProperty('allColumns')) {
                // So all columns are seen as "used" because that are all available.
                select = this.supportData.allColumns;
            }
                // Otherwise we merge the list config "select" with the "default columns" of the list template.
                // For example, in the list template "Ausgeführte Aktionen", there are pre defined (static) selects
            // set by the list template and also user chosen selects.
            else {
                select = [
                    ...(this.listConfig.data.source.select || []),
                    ...this.supportData.defaultColumns || []
                ];
            }

            let usedColumnAliases = [];

            for (let i = 0; i < select.length; i++) {
                let parts = [];

                // In SQL-Query Builder the selects are strings
                // With Realtion or Accesses Query Builder, these are objects, e.g. {'data' : 'name', 'alias' : 'roles_name'}.
                if (typeof select[i] === 'string' && select[i].includes(' as ')) {
                    parts = select[i].split(' as ');
                }
                else if (typeof select[i] === 'object') {
                    parts = [
                        // Either "data" or "column" (when supportData.allColumn is used)
                        select[i].data || select[i].column,
                        select[i].alias
                    ];
                }

                if (parts.length === 2) {
                    usedColumnAliases = [
                        ...usedColumnAliases,
                        {
                            column: parts[0],
                            alias: parts[1]
                        }
                    ];
                }
            }

            return usedColumnAliases;
        },
        aliasLabels: function () {
            let aliasLabels = {};

            for (let i = 0; i < (this.supportData.allColumns || []).length; i++) {
                let column = this.supportData.allColumns[i];
                aliasLabels[column.alias] = column.label;
            }

            return aliasLabels;
        },
        showPasteListButton() {
            return (this.getCopyElement())?.name === 'list_config';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        updateSupportData(listConfigId) {
            this.supportFetched = false;
            this.fetchListSupportData(listConfigId).then((response) => {
                this.supportData = response.data;
                this.supportFetched = true;
            });
        },
        onChangeListConfig(e) {
            this.listConfig = this.definition.list_configs.find(ele => ele.id === e.target.value);

            this.updateSupportData(this.listConfig.id);
            this.updateNavigation({
                detail: this.listConfig.id,
                lastViewedModelOfType: {listConfig: this.listConfig.id}
            });
        },
        onUpdateSubNavigation(name, newListConfigId) {
            let detail = this.listConfig ? this.listConfig.id : null;

            // Falls Variable gesetzt und anders als aktuelle
            if (newListConfigId) {
                detail = newListConfigId;
            }

            this.clearError();
            this.detailComponentName = name;
            this.updateNavigation({
                sub: name,
                detail: detail,
                lastViewedModelOfType: {listConfig: detail}
            });

            if (newListConfigId) {
                this.listConfig = this.definition.list_configs.find(ele => ele.id === newListConfigId);
                this.updateSupportData(this.listConfig.id);
            }
        },
        onUpdateData(part, value) {
            this.listConfig = {
                ...this.listConfig,
                data: {
                    ...this.listConfig.data,
                    [part]: value
                }
            };

            this.patchDefinition('UpdateListConfig', this.listConfig).catch(() => {
            });
        },
        onUpdateMeta(part, value) {
            if (![
                'name',
                'slug',
                'description',
                'roles'
            ].includes(part)) {
                return;
            }

            this.listConfig = {
                ...this.listConfig,
                [part]: value
            };

            this.patchDefinition('UpdateListConfig', this.listConfig).catch(() => {
            });
        },
        onUpdateSource(part, value) {
            this.onUpdateData('source', {
                ...this.listConfig.data.source,
                [part]: value
            });
        },
        onUpdateMultipleSource(updates) {
            this.onUpdateData('source', {
                ...this.listConfig.data.source, ...updates
            });
        },
        onUpdateColumns(columns) {
            this.onUpdateData('columns', [...columns]);
        },
        onDeleteListConfig() {
            this.patchDefinition('DeleteListConfig', {id: this.listConfig.id}).then(() => {
                if (this.definition.list_configs.length) {
                    this.listConfig = this.definition.list_configs[0];
                    this.updateSupportData(this.listConfig.id);
                }
                else {
                    this.listConfig = null;
                }
            });
        },
        copyList() {
            this.saveCopyElement('list_config', this.listConfig);
        },
        pasteList() {
            let data = {
                name: 'list_config',
                options: {}
            };
            this.patchDefinition('PasteElement', data).then(() => {
                let index = this.definition.list_configs.length - 1;
                this.listConfig = this.definition.list_configs[index];
                this.updateSupportData(this.listConfig.id);
            }).catch(() => {
            });
        }
    },
    mounted() {
        // sub-Navigation
        if (this.ui.navigation.sub && this.components.includes(this.ui.navigation.sub)) {
            this.detailComponentName = this.ui.navigation.sub;
        }
        else {
            this.detailComponentName = 'Options';
        }

        // detail-Navigation
        let detail = this.ui.navigation.detail;
        let lastViewedId = this.ui.navigation.lastViewedModelOfType.listConfig || '';
        let listConfig = detail ? this.list_configs.find(ele => ele.id === detail) : (this.list_configs.length ? this.list_configs[0] : null);

        if (listConfig) {
            this.listConfig = listConfig;
            this.updateSupportData(listConfig.id);
        }
        else {
            if (lastViewedId && this.list_configs.find(ele => ele.id === lastViewedId)) {
                this.listConfig = this.list_configs.find(ele => ele.id === lastViewedId);
                this.updateSupportData(this.listConfig.id);
            }
            else {
                if (this.list_configs.length) {
                    this.listConfig = this.list_configs[0];
                    this.updateSupportData(this.list_configs[0].id);
                }
            }
        }

        this.updateNavigation({
            sub: this.detailComponentName,
            detail: this.listConfig ? this.listConfig.id : null,
            lastViewedModelOfType: {listConfig: this.listConfig ? this.listConfig.id : null}
        });
    }
};
</script>
