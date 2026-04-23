<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row d-flex justify-content-between">
            <div class="col-3">
                <button v-if="mode === null && ui.editable" class="btn btn-sm btn-primary mb-2" @click="createPage">
                    Menüpunkt hinzufügen
                </button>
                <ul class="list-group">
                    <li v-for="(mainPage, index) in menuItems"
                        :class="'list-group-item mb-3 p-2 ' + (activeId === mainPage.id ? 'border border-primary' : 'border')">
                        <div class="d-flex content-align-center justify-content-between">
                            <div>
                                <span v-if="mainPage.image" class="material-icons mr-1">{{ mainPage.image }}</span>
                                <a href="#" @click="open(mainPage)">
                                    <span class="font-weight-bold">{{ mainPage.label }}</span>
                                </a>
                            </div>
                            <div class="btn-group float-right" role="group" aria-label="Controls">
                                <button type="button" disabled class="btn bg-white border-0 py-0 btn-sm btn-light text-primary" v-if="mainPage.conditions.length">
                                    <span class="material-icons text-primary">visibility</span>
                                </button>
                                <button v-if="mode === null && menuItems.length && mainPage.sort > lowestSort && ui.editable" type="button" class="py-0 btn btn-sm" @click="changeSort(index, 'down')">
                                    <span class="material-icons text-muted">expand_less</span>
                                </button>
                                <button v-if="mode === null && menuItems.length && mainPage.sort < highestSort && ui.editable" type="button" class="py-0 btn btn-sm" @click="changeSort(index, 'up')"><span class="material-icons text-muted">expand_more</span></button>
                                <button v-if="mode === null && ui.editable" type="button" class="py-0 btn btn-sm" @click="deletePage(mainPage.id)">
                                    <span class="material-icons text-danger">close</span>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div v-if="mode !== 'new' && mode !== 'edit'" class="col-1 d-flex justify-content-end">
                <Docs class="mr-2" article="navigation"/>
            </div>
            <div class="col-9">
                <div class="row" v-if="mode === 'new' || mode === 'edit'">
                    <div class="col">
                        <div class="form-group row">
                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <Docs class="mr-2" article="navigation"/>
                                </div>
                                <label for="label" class="mb-0">Label</label>
                                <input type="text" v-model="detailPage.label" class="form-control form-control-sm" id="label" name="label" placeholder="" required :readonly="!ui.editable">
                                <div v-for="error in (ui.validationErrors.label || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label for="url" class="mb-0">URL</label>
                                <div class="form-group input-group-sm mb-2">
                                    <textarea class="form-control d-block mb-2" rows="3" v-model="detailPage.url" :readonly="!ui.editable"></textarea>
                                    <OptionBadgesWithText :value="detailPage.url" display-block hide-on-empty />
                                </div>
                                <div class="form-group input-group-sm mb-2">
                                    <DropdownSelector
                                        :syntax-include="['process.outputs', 'variables', 'process.status', 'faker', 'reference.outputs', 'reference.relation_data', 'process.urls', 'reference.urls', 'graphs.urls']"
                                        @selected="setUrl($event)" v-if="ui.editable"/>
                                </div>
                                <small class="form-text text-muted">Relative URLs mit "/" und absolute URLs mit
                                    "https://" beginnen. Menü-Punkte mit leerer URL werden nicht angezeigt.</small>
                                <div v-for="error in (ui.validationErrors.url || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label class="mb-0">Icon</label>
                                <IconSelection :require-selection="true" :selected="detailPage.image" @on-select-icon="onIconUpdate" :editable="ui.editable"/>
                                <div v-for="error in (ui.validationErrors.image || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label class="form-check-label" for="target">Klick-Verhalten</label>
                                <div class="form-group">
                                    <select class="form-control" id="target" v-model="detailPage.target" :disabled="!ui.editable">
                                        <option value="_self">Gleicher Tab</option>
                                        <option value="_blank">Neuer Tab</option>
                                        <option value="popup">Neues Fenster</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label class="mb-0">Konditionen</label>
                                <div class="mb-3">
                                    <ConditionsTable :conditions="[...conditions]" @delete-item="onDeleteCondition" :editable="ui.editable"/>
                                </div>
                                <ConditionsAdd :conditions="[...conditions]"
                                               :syntax-loader-include="['process.outputs', 'variables', 'process.status', 'reference.outputs', 'reference.relation_data', 'reference.status']"
                                               @add-condition="onConditionAdd" v-if="ui.editable"
                                />
                                <small class="text-muted">Ohne Konditionen wird der Menü-Punkt immer angezeigt.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-end" v-if="!sending && !error">
                            <button type="button" class="btn btn-sm btn-outline-danger mr-2" @click="cancelEdit">
                                Abbrechen
                            </button>
                            <button type="button" class="btn btn-sm btn-success" @click="mode === 'edit' ? updatePage() : addPage()" v-if="ui.editable">
                                {{ mode === 'edit' ? 'Speichern' : 'Hinzufügen' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import VueTagsInput from '@johmun/vue-tags-input';
import {mapActions, mapGetters} from "vuex";
import utils from '../../config-utils';
import {reduxActions} from "../../store/develop-and-config";
import LoadingSeparator from "./LoadingSeparator";
import IconSelection from "../utils/IconSelection";
import ConditionsTable from "./partials/ConditionsTable";
import ConditionsAdd from "./partials/ConditionsAdd";
import DropdownSelector from "../utils/DropdownSelector";
import OptionBadgesWithText from "../utils/OptionBadgesWithText";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        OptionBadgesWithText,
        DropdownSelector,
        ConditionsAdd,
        ConditionsTable,
        IconSelection,
        LoadingSeparator,
        VueTagsInput
    },
    data() {
        return {
            navigation: null,
            listConfigs: null,
            activeId: null,
            mode: null,
            detailPage: {},
            sending: false,
            error: null,
            errorMessage: ''
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'action_types',
            'outputs',
            'ui',
            'status_types',
            'relation_types_with_single_process',
            'graphs_output_names'
        ]),
        lowestSort() {
            if (!this.definition.menu_items.length) {
                return 0;
            }
            return Math.min(...this.definition.menu_items.map(ele => ele.sort));
        },
        highestSort() {
            if (!this.definition.menu_items.length) {
                return 0;
            }
            return Math.max(...this.definition.menu_items.map(ele => ele.sort));
        },
        menuItems() {
            return [...this.definition.menu_items].sort((a, b) => a.sort > b.sort ? 1 : -1);
        },
        conditions() {
            return (this.detailPage || {}).conditions || [];
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        createPage() {
            this.activeId = null;
            this.mode = 'new';
            this.detailPage = {
                label: '',
                url: '',
                image: 'arrow_forward_ios',
                sort: this.highestSort + 1,
                route_name: 'process.show',
                target: '_self'
            };
        },
        open(item) {
            this.activeId = item.id;
            this.mode = 'edit';
            this.detailPage = {...item};
        },
        changeSort(index, direction) {
            let menuItems = [...this.menuItems];
            let menuItem = menuItems[index];
            let currentSort = menuItem.sort;
            let targetSort = direction === 'down' ? currentSort - 1 : currentSort + 1;
            let indexOfSwappingElement = menuItems.findIndex(ele => ele.sort === targetSort);

            // Wenn bereits aussen und dann weiter nach aussen sortiert werden möchte
            if ((direction === 'down' && currentSort === 0) || (direction === 'up' && currentSort === this.highestSort)) {
                return;
            }

            // Spalte mit der Zielsortierung aktualisieren
            menuItems[indexOfSwappingElement] = {
                ...menuItems[indexOfSwappingElement],
                sort: currentSort
            };

            // Zu ändernde Spalte mit der neuen Sortierung aktualisieren
            menuItems[index] = {
                ...menuItems[index],
                sort: targetSort
            };

            this.patchDefinition('UpdateMenuItems', {items: menuItems}).then(() => {
                this.mode = null;
                this.activeId = null;
            }).catch(() => {
            });
        },
        updatePage() {
            this.patchDefinition('UpdateMenuItem', {...this.detailPage}).then(() => {
                this.mode = null;
                this.activeId = null;
            }).catch(() => {
            });
        },
        setUrl(url) {
            if (url.value === '[[process.meta.url') {
                this.detailPage.route_name = 'process.show';
            }

            else if (url.value === '[[process.meta.data_url') {
                this.detailPage.route_name = 'process.data';
            }

            else if (url.value.startsWith('[[process.urls.lists.')) {
                this.detailPage.route_name = 'process.list';
            }

            else {
                this.detailPage.route_name = '';
            }

            this.detailPage.url = this.detailPage.url + this.setSyntaxLabel(url.value, url.label);
        },
        addPage() {
            this.patchDefinition('StoreMenuItem', {...this.detailPage}).then(() => this.mode = null).catch(() => {
            });
        },
        deletePage(id) {
            this.patchDefinition('DeleteMenuItem', {id}).catch(() => {
            });
        },
        onIconUpdate(image) {
            this.detailPage.image = image;
        },
        onConditionAdd(condition) {
            this.detailPage = {
                ...this.detailPage,
                conditions: [...this.detailPage.conditions, condition]
            };
        },
        onDeleteCondition(item) {
            let conditions = [...this.detailPage.conditions].filter(function (ele) {
                return JSON.stringify(ele) !== JSON.stringify(item);
            });

            this.detailPage = {
                ...this.detailPage,
                conditions: [...conditions]
            };
        },
        cancelEdit() {
            this.mode = null;
            this.activeId = null;
        }
    }
};
</script>
