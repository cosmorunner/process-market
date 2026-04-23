<template>
    <div class="dropdown d-inline-block" ref="dropdown">
        <button :class="'btn btn-sm btn-outline-primary dropdown-toggle ' + (!roundedBorders ? 'rounded-0' : '')"
                data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false" style="height:29px;">
            <span class="material-icons" v-if="!displaySelectedValue || lastSelectedValue === null">{{ icon }}</span>
            <OptionBadges v-else :value="lastSelectedValue"/>
        </button>
        <div class="dropdown-menu custom-selector bg-light" :style="dropdownWidth ? 'width:' + dropdownWidth + 'px;' : ''">
            <div v-if="displayCustomSelections" class="input-group mb-3 px-2">
                <input type="text" class="form-control form-control-sm" aria-label="" v-model="customItem.value"
                       placeholder="Leere Zeichenkette" :readonly="customItem.type !== 'custom_value'"
                       @keyup.enter="addCustomItem">
                <div class="input-group-append">
                    <select id="customItemDropdownMenu" class="form-control form-control-sm" v-model="customItem.type"
                            @change="setCustomItem($event)">
                        <option value="custom_value">Eigener Wert</option>
                        <option value="bool|TRUE">TRUE</option>
                        <option value="bool|FALSE">FALSE</option>
                        <option value="null|NULL">NULL</option>
                    </select>
                </div>
                <div class="input-group-append">
                    <button class="btn btn-sm rounded-right btn-outline-success" @click="addCustomItem">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
            <div class="input-group mb-1 px-2">
                <input v-model="search" type="text" class="form-control form-control-sm" placeholder="Suche..."
                       aria-label="Search" aria-describedby="Search">
                <div class="input-group-append" v-if="search">
                    <button class="btn btn-sm btn-outline-danger" type="button" id="button-addon2" @click="search = ''">
                        <small class="material-icons">close</small>
                    </button>
                </div>
            </div>
            <div class="px-2 pt-2 pb-1"
                 v-if="syntaxKeys.length > 1 || pipeKeys.length > 1 || (syntaxKeys.length && pipeKeys.length)">
                <button type="button"
                        :class="'btn btn-sm btn-outline-primary mr-1 mb-1 px-1 py-0 ' + (selectedSyntaxCategory === null && selectedPipeCategory === null ? 'active' : '')"
                        @click="clearCategory">
                    Alle
                </button>
                <button type="button" v-if="syntaxKeys.length > 1 || pipeKeys.length"
                        :class="'btn btn-sm btn-outline-primary mr-1 mb-1 px-1 py-0 ' + (selectedSyntaxCategory === key ? 'active' : '')"
                        v-for="key in syntaxKeys" @click="setSyntaxCategory(key)">
                    {{ syntaxLoaderLabels()[key] }}
                </button>
                <button type="button" v-if="pipeKeys.length > 1 || syntaxKeys.length"
                        :class="'btn btn-sm btn-outline-primary mr-1 mb-1 px-1 py-0 ' + (selectedPipeCategory === key ? 'active' : '')"
                        v-for="key in pipeKeys" @click="setPipeCategory(key)">
                    {{ pipeLoaderLabels()[key] }}
                </button>
            </div>
            <div :class="'scrollable-dropdown bg-white border-top ' + (fetching ? 'opacity-3' : '')" v-if="!edit">
                <div class="dropdown-item d-flex justify-content-between px-2 py-0" v-for="item in filteredItems">
                    <span class="flex-grow-1 mouse-pointer" @click="onSelectItem(item)">{{ item.text }}</span>
                    <span class="btn btn-sm btn-light bg-white text-primary py-1 mr-1" @click="() => edit = item">
                        <span class="material-icons">edit</span>
                    </span>
                </div>
            </div>

            <div class="px-2 py-1" v-if="fetching && emptyAutocompleteList">
                <img src="/img/loading.gif" alt="Loading" width="14.4" height="14.4"/>
            </div>
            <template v-if="!fetching && omittedItems">
                <div class="border-top px-2 py-1">
                    <small class="text-muted">Viele Einträge, bitte Suche nutzen.</small>
                </div>
            </template>
            <template v-if="emptyAutocompleteList && !fetching && hasCategorySelected">
                <div class="px-2 py-1">
                    <small class="text-muted">Keine Einträge.</small>
                    <small class="d-inline-block ml-2 mouse-pointer" v-if="search" @click="search = ''">Suche löschen</small>
                    <small class="d-inline-block ml-2 mouse-pointer" v-if="hasCategorySelected" @click="clearCategory">Kategorie löschen</small>
                </div>
            </template>
            <template v-if="emptyAutocompleteList && !fetching && !hasCategorySelected && search.length >= 2">
                <div class="px-2 py-1">
                    <small class="text-muted">Keine Einträge.</small>
                </div>
            </template>
            <template v-if="emptyAutocompleteList && !fetching && !hasCategorySelected && search.length < 2">
                <div class="px-2 py-1">
                    <small class="text-muted">Kategorie wählen oder Suche nutzen (mind. 2 Zeichen).</small>
                </div>
            </template>
            <div v-if="edit" class="px-2">
                <button class="btn btn-sm btn-link px-0" @click="() => edit = null">Zurück</button>
                <div class="form-group mb-2">
                    <label class="mb-0">Wert</label>
                    <input type="text" class="form-control form-control-sm" v-model="edit.value">
                </div>
                <div class="form-group mb-0">
                    <label class="mb-0">Label</label>
                    <input type="text" class="form-control form-control-sm" v-model="edit.label">
                </div>
                <div class="d-flex mt-2 justify-content-end">
                    <button class="btn btn-sm btn-success float-right" @click="addCustom">
                        <span class="material-icons">done</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import VueTagsInput from '@johmun/vue-tags-input';
import utils from "../../config-utils";
import OptionBadges from "./OptionBadges";
import {reduxActions} from "../../store/develop-and-config";

export default {
    components: {
        OptionBadges,
        VueTagsInput
    },
    props: {
        label: String,
        icon: {
            default: 'add',
            type: String
        },
        dropdownWidth: {
            default: null,
            type: Number | null
        },
        displaySelectedValue: {
            default: false,
            type: Boolean
        },
        roundedBorders: {
            default: true,
            type: Boolean
        },
        actionType: Object,
        syntaxInclude: {
            default: () => [],
            type: Array
        },
        pipeInclude: {
            default: () => [],
            type: Array
        },
        displayCustomSelections: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            fetching: false,
            search: '',
            lastSelectedValue: null,
            selectedSyntaxCategory: null,
            selectedPipeCategory: null,
            syntaxValues: {},
            pipeValues: {},
            debounce: null,
            omittedItems: false,
            edit: null,
            customItem: {
                type: 'custom_value',
                value: ''
            }
        };
    },
    computed: {
        ...mapGetters(['ui']),
        syntaxKeys() {
            return Object.keys(this.syntaxLoaderLabels()).filter(ele => this.syntaxInclude.includes(ele));
        },
        pipeKeys() {
            return Object.keys(this.pipeLoaderLabels()).filter(ele => this.pipeInclude.includes(ele));
        },
        hasCategorySelected() {
            return this.selectedSyntaxCategory || this.selectedPipeCategory;
        },
        syntaxValuesArr() {
            // Wenn deaktiviert oder eine PipeCategory aktiv ist.
            if (this.selectedPipeCategory) {
                return [];
            }

            if (this.selectedSyntaxCategory) {
                return this.syntaxValues[this.selectedSyntaxCategory] || [];
            }

            let that = this;

            return this.syntaxKeys.reduce(function (carry, key) {
                return [
                    ...carry,
                    ...that.syntaxValues[key] || []
                ];
            }, []);
        },
        pipeValuesArr() {
            // Wenn deaktiviert oder eine SyntaxCategory aktiv ist.
            if (this.selectedSyntaxCategory) {
                return [];
            }

            if (this.selectedPipeCategory) {
                return this.pipeValues[this.selectedPipeCategory] || [];
            }

            let that = this;

            return this.pipeKeys.reduce(function (carry, key) {
                return [
                    ...carry,
                    ...that.pipeValues[key] || []
                ];
            }, []);
        },
        autoCompleteItems() {
            let items = [
                ...this.pipeValuesArr,
                ...this.syntaxValuesArr
            ];

            return items.map(function (ele) {
                ele.text = ele.label;

                return ele;
            });
        },
        emptyAutocompleteList() {
            return this.filteredItems.filter(ele => ele.value.length).length === 0;
        },
        filteredItems() {
            let that = this;

            return this.autoCompleteItems.filter(function (i) {
                if (that.selectedPipeCategory) {
                    return i.part === that.selectedPipeCategory;
                }
                if (that.selectedSyntaxCategory) {
                    return i.part === that.selectedSyntaxCategory;
                }

                return i;
            });
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        clearCategory() {
            this.syntaxValues = {};
            this.pipeValues = {};
            this.selectedSyntaxCategory = null;
            this.selectedPipeCategory = null;

            if (this.search.length >= 2) {
                this.fetchItems(this.syntaxKeys, this.pipeKeys);
            }
        },
        setSyntaxCategory(key) {
            this.syntaxValues = {};
            this.selectedSyntaxCategory = key;
            this.selectedPipeCategory = null;
            this.edit = null;
            this.loadSyntaxAndPipeValuesForKey(key, null);
        },
        setPipeCategory(key) {
            this.pipeValues = {};
            this.selectedPipeCategory = key;
            this.selectedSyntaxCategory = null;
            this.edit = null;
            this.loadSyntaxAndPipeValuesForKey(null, key);
        },
        onSelectItem(item) {
            item.value_with_label = this.setSyntaxLabel(item.value, item.label);
            this.lastSelectedValue = item.value_with_label;
            $(this.$refs['dropdown']).dropdown('toggle');
            this.$emit('selected', item);
        },
        clearLastSelectedValue() {
            this.customItem.value = '';
            this.lastSelectedValue = null;
        },
        addCustom() {
            let item = {...this.edit};

            item.value_with_label = this.setSyntaxLabel(item.value, item.label);
            this.lastSelectedValue = item.value_with_label;
            $(this.$refs['dropdown']).dropdown('toggle');
            this.$emit('selected', item);
            this.edit = null;
        },
        setCustomItem($event) {
            let value = $event.target.value;

            switch (value) {
                case 'bool|TRUE':
                    this.customItem.value = 'TRUE';
                    break;
                case 'bool|FALSE':
                    this.customItem.value = 'FALSE';
                    break;
                case 'null|NULL':
                    this.customItem.value = 'NULL';
                    break;
                case 'custom_value':
                    this.customItem.value = '';
                    break;
            }
        },
        addCustomItem() {
            let item = {};

            if (this.customItem.type === 'custom_value') {
                item.value_with_label = this.customItem.value;
                this.lastSelectedValue = this.customItem.value;
            }
            else {
                item.value_with_label = this.customItem.type;
                this.lastSelectedValue = this.customItem.type;
            }

            $(this.$refs['dropdown']).dropdown('toggle');

            this.$emit('selected', item);
        },
        loadSyntaxAndPipeValuesForKey(syntaxKey, pipeKey) {
            // Syntax und Pipe Keys ermitteln, die geladen werden sollen und bisher noch nicht geladen wurden.
            let syntaxPartsToLoad = syntaxKey ? [syntaxKey] : [];
            let pipePartsToLoad = pipeKey ? [pipeKey] : [];

            // Nur Syntax-Werte laden, wenn diese zuvor noch nicht geladen wurden.
            if (syntaxPartsToLoad.length > 0 || pipePartsToLoad.length > 0) {
                this.fetchItems(syntaxPartsToLoad, pipePartsToLoad);
            }
        },
        fetchItems(syntaxParts = [], pipeParts = []) {
            clearTimeout(this.debounce);

            let that = this;
            let data = {
                search: this.search.length >= 2 ? this.search : null,
                action_type_id: this.actionType ? this.actionType.id : null,
                syntax_parts: syntaxParts,
                pipe_parts: pipeParts
            };

            that.fetching = true;

            this.debounce = setTimeout(() => {
                axios.post('/api/process-versions/' + that.ui.processVersionId + '/syntax-values', data).then(function (response) {
                    that.syntaxValues = response.data.syntax;
                    that.pipeValues = response.data.pipe;
                    that.omittedItems = response.data.omittedItems
                    that.fetching = false;
                }).catch(function (error) {
                    that.fetching = false;
                    console.log(error);
                });
            }, 350);
        },
    },
    mounted() {
        $('.dropdown-menu.custom-selector').on("click.bs.dropdown", function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
    },
    watch: {
        search: {
            handler(newVal) {
                this.edit = null;

                // No category
                if (!this.hasCategorySelected) {
                    if (newVal.length >= 2) {
                        this.fetchItems(this.syntaxKeys, this.pipeKeys);
                    }
                    else {
                        this.omittedItems = false;
                        this.syntaxValues = {};
                        this.pipeValues = {};
                    }
                }
                else if (!newVal || newVal.length >= 2) {
                    let syntaxPartsToLoad = this.selectedSyntaxCategory ? [this.selectedSyntaxCategory] : [];
                    let pipePartsToLoad = this.selectedPipeCategory ? [this.selectedPipeCategory] : [];
                    this.fetchItems(syntaxPartsToLoad, pipePartsToLoad);
                }
            }
        }
    },
};
</script>

<style scoped>

.dropdown-menu {
    min-width: 320px;
}

.dropdown-menu button.dropdown-item {
    font-size: 0.8rem;
}
</style>

