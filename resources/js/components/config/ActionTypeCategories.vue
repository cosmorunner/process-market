<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-start">
                    <button class="btn btn-primary btn-sm mb-2" @click="openAddCategoryModal" v-if="ui.editable">
                        Kategorie anlegen
                    </button>
                    <Docs class="mr-2" article="action-categories"/>
                </div>
                <div class="row mb-2" v-for="category in sortedCategories">
                    <div class="col">
                        <div class="rounded-0 card h-100">
                            <div
                                :class="'card-header px-2 py-1 d-flex justify-content-between align-items-center border-primary ' + (ui.editable ? 'hover-pointer' : '')">
                                <span class="text-primary flex-grow-1 text-truncate disable-user-select"
                                      @click="onOpenEditModal(category)">
                                    <span v-if="category.color" class="material-icons" :style="'color:' + category.color + ';'">brightness_1</span>
                                    <span v-if="category.image" class="material-icons">{{ category.image }}</span>
                                    <span>{{ category.name }}</span>
                                    <span v-if="category.hidden" class="badge badge-light">Versteckt</span>
                                    <span v-if="category.locked" class="badge badge-light">System-Kategorie</span>
                                </span>
                                <div>
                                    <button class="btn btn-sm btn-outline-light mr-1" @click="sortCategory('decrease', category)" v-if="ui.editable">
                                        <span class="material-icons text-primary">keyboard_arrow_up</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-light mr-1" @click="sortCategory('increase', category)" v-if="ui.editable">
                                        <span class="material-icons text-primary">keyboard_arrow_down</span>
                                    </button>
                                    <span v-if="category.locked" class="material-icons text-muted">lock</span>
                                    <button v-if="!category.locked && ui.editable" class="btn btn-sm btn-light text-danger p-0" @click="deleteCategory(category.id)">
                                        <span class="material-icons">close</span>
                                    </button>
                                </div>
                            </div>
                            <div :class="ui.editable ? 'card-body p-2 hover-pointer' : 'card-body p-2'">
                                <span class="text-muted d-block mb-2" @click="ui.editable ? () => onOpenEditModal(category) : () => {}">{{ category.description || '&nbsp;' }}</span>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item p-2 d-flex justify-content-between" :key="actionType.id"
                                        v-for="actionType in actionTypesByCategory(category.id)">
                                        <div>
                                            <span>{{ actionType.name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between" v-if="ui.editable">
                                            <button class="btn btn-sm btn-outline-light mr-1"
                                                    @click="sortActionType('decrease', actionType, category.id)">
                                                <span class="material-icons text-primary">keyboard_arrow_up</span>
                                            </button>
                                            <button class="btn btn-sm btn-outline-light mr-1"
                                                    @click="sortActionType('increase', actionType, category.id)">
                                                <span class="material-icons text-primary">keyboard_arrow_down</span>
                                            </button>
                                            <select class="custom-select custom-select-sm" @change="changeCategory($event, actionType)">
                                                <option value="" selected>Verschieben nach...</option>
                                                <option :value="cat.id"
                                                        v-for="cat in (categories.filter(ele => ele.id !== category.id))">
                                                    {{ cat.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CategoryModal from "./CategoryModal";
import LoadingSeparator from "./LoadingSeparator";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        LoadingSeparator
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'categories'
        ]),
        sortedCategories() {
            return [...this.categories].sort((a, b) => a.sort > b.sort);
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        changeCategory(e, actionType) {
            if (e.target.value !== '') {
                let categoryActionTypes = this.actionTypesByCategory(e.target.value);
                let actionTypeSorts = [...categoryActionTypes.map(ele => ele.sort)];
                let sort = Math.max.apply(Math, (!actionTypeSorts.length ? [0] : actionTypeSorts)) + 1;

                let payload = {
                    ...actionType,
                    category_id: e.target.value,
                    sort: sort || 0
                };

                this.patchDefinition('UpdateActionType', payload).catch(() => {
                });
            }
        },
        openAddCategoryModal() {
            this.openModal({
                componentName: 'CategoryModal',
                data: {
                    category: null
                }
            });
        },
        onOpenEditModal(category) {
            this.openModal({
                componentName: 'CategoryModal',
                data: {
                    category: category
                }
            });
        },
        deleteCategory(id) {
            this.patchDefinition('DeleteCategory', {id}).catch(() => {
            });
        },
        actionTypesByCategory(categoryId) {
            return this.action_types.filter(ele => ele.category_id === categoryId).sort((a, b) => a.sort > b.sort);
        },
        sortCategory(direction, category) {
            let categories = [...this.sortedCategories];
            let currentSort = category.sort;
            let currentSorts = categories.map(ele => ele.sort);
            let currentIndex = currentSorts.indexOf(currentSort);
            let targetIndex = direction === 'increase' ? currentIndex + 1 : currentIndex - 1;
            let targetSort = currentSorts[targetIndex];

            // Falls ganz vorne oder ganz hinten nach oben bzw. unten verschoben werden möchte.
            if (typeof targetSort === 'undefined') {
                return;
            }

            // Mit dem Aktionstyp swappen der an dem Zielplatz ist.
            let updatedCategories = categories.map(function (ele) {
                if (ele.sort === targetSort && ele.id !== category.id) {
                    ele = {
                        ...ele,
                        sort: currentSort
                    };
                }
                if (ele.id === category.id) {
                    ele = {
                        ...ele,
                        sort: targetSort
                    };
                }

                return ele;
            });

            this.patchDefinition('UpdateCategories', {items: updatedCategories}).catch(() => {
            });
        },
        sortActionType(direction, actionType, categoryId) {
            let categoryActionTypes = [...this.actionTypesByCategory(categoryId)];
            let currentSort = actionType.sort;
            let currentSorts = categoryActionTypes.map(ele => ele.sort);
            let currentIndex = currentSorts.indexOf(currentSort);
            let targetIndex = direction === 'increase' ? currentIndex + 1 : currentIndex - 1;

            // Falls ganz vorne oder ganz hinten nach oben bzw. unten verschoben werden möchte.
            if (typeof currentSorts[targetIndex] === 'undefined') {
                return;
            }
            let targetSort = currentSorts[targetIndex];

            // Mit dem Aktionstyp swappen der an dem Zielplatz ist.
            let updatedActionTypes = categoryActionTypes.map(function (ele) {
                if (ele.sort === targetSort && ele.id !== actionType.id) {
                    ele = {
                        ...ele,
                        sort: currentSort
                    };
                }
                if (ele.id === actionType.id) {
                    ele = {
                        ...ele,
                        sort: targetSort
                    };
                }

                return ele;
            });

            this.patchDefinition('UpdateActionTypes', {items: updatedActionTypes}).catch(() => {
            });
        }
    }
};
</script>
