<template>
    <div class="form-group input-group-sm mb-2">
        <label class="mb-0" v-if="label">{{ label }}</label>
        <div class="input-group">
            <div class="input-group-prepend" v-if="icon">
                    <span class="input-group-text">
                        <span class="material-icons">{{ icon }}</span>
                    </span>
            </div>
            <vue-tags-input v-model="tag" :tags="tags" :autocomplete-items="filteredItems" :disabled="!editable"
                            :add-only-from-autocomplete="addOnlyFromAutocomplete" :max-tags="maxItems"
                            :autocomplete-min-length="0" :save-on-key="[13, ';']" :placeholder="placeholderValue"
                            @tags-changed="tagsChanged">
                <div slot="autocomplete-header" class="bg-light pt-1 border-bottom">
                    <div v-if="!addOnlyFromAutocomplete" class="px-2 pt-2">
                        <small class="text-muted">Eigenen Wert mit Eingabe-Taste bestätigen.</small>
                    </div>
                    <div class="px-2 pt-2 pb-1"
                         v-if="(syntaxKeys.length || pipeKeys.length) || (syntaxKeys.length && pipeKeys.length)">
                        <button type="button" v-if="allKeys.length > 1"
                                :class="'btn btn-sm btn-outline-primary mr-1 mb-1 px-1 py-0 ' + (selectedSyntaxCategory === null && selectedPipeCategory === null && selectedManualCategory === null ? 'active' : '')"
                                @click="clearCategory">
                            Alle
                        </button>
                        <button type="button" v-if="manualKeys.length"
                                :class="'btn btn-sm btn-outline-dark mr-1 mb-1 px-1 py-0 ' + (selectedManualCategory === key ? 'active' : '')"
                                v-for="key in manualKeys" @click="setManualCategory(key)">
                            {{ manualLabels()[key] }}
                        </button>
                        <button type="button" v-if="syntaxKeys.length"
                                :class="'btn btn-sm btn-outline-dark mr-1 mb-1 px-1 py-0 ' + (selectedSyntaxCategory === key ? 'active' : '')"
                                v-for="key in syntaxKeys" @click="setSyntaxCategory(key)">
                            {{ syntaxLoaderLabels()[key] }}
                        </button>
                        <button type="button" v-if="pipeKeys.length"
                                :class="'btn btn-sm btn-outline-dark mr-1 mb-1 px-1 py-0 ' + (selectedPipeCategory === key ? 'active' : '')"
                                v-for="key in pipeKeys" @click="setPipeCategory(key)">
                            {{ pipeLoaderLabels()[key] }}
                        </button>
                    </div>
                </div>
                <div slot="autocomplete-item" slot-scope="props" :class="'d-flex justify-content-between p-0 ' + (fetching ? 'opacity-3' : '')">
                    <span class="flex-grow-1 px-2 py-1" @click="props.performAdd(props.item)">
                        <span>{{ props.item.text }}</span>
                    </span>
                    <span class="btn btn-sm btn-light bg-white text-primary py-1 mr-1" @click="() => edit = props.item">
                        <span class="material-icons">edit</span>
                    </span>
                </div>
                <div slot="autocomplete-footer" class="bg-light pb-2" slot-scope="props">
                    <div class="px-2 py-1" v-if="fetching && emptyAutocompleteList">
                        <img src="/img/loading.gif" alt="Loading" width="14.4" height="14.4"/>
                    </div>
                    <template v-if="omittedItems && !emptyAutocompleteList">
                        <div class="border-top px-2 py-1">
                            <small class="text-muted">Viele Einträge, bitte Suche nutzen.</small>
                        </div>
                    </template>
                    <template v-if="emptyAutocompleteList && !fetching && hasCategorySelected">
                        <div class="px-2 py-1">
                            <small class="text-muted">Keine Einträge.</small>
                        </div>
                    </template>
                    <template v-if="emptyAutocompleteList && !fetching && !hasCategorySelected && tag.length >= 2">
                        <div class="px-2 py-1">
                            <small class="text-muted">Keine Einträge.</small>
                        </div>
                    </template>
                    <template v-if="emptyAutocompleteList && !fetching && !hasCategorySelected && tag.length < 2">
                        <div class="px-2 py-1">
                            <small class="text-muted">Kategorie wählen oder Suche nutzen (mind. 2 Zeichen).</small>
                        </div>
                    </template>
                    <div v-if="edit" class="border-top p-2">
                        <div class="form-group mb-2">
                            <label class="mb-0">Wert</label>
                            <input type="text" class="form-control form-control-sm" v-model="edit.value">
                        </div>
                        <div class="form-group mb-0">
                            <label class="mb-0">Label</label>
                            <input type="text" class="form-control form-control-sm" v-model="edit.label">
                        </div>
                        <div class="d-flex mt-2 justify-content-end">
                            <button class="btn btn-sm btn-success float-right" @click="addCustom([...tags, edit])">
                                <span class="material-icons">done</span>
                            </button>
                        </div>
                    </div>
                </div>
            </vue-tags-input>
        </div>
        <small class="text-muted" v-if="help">{{ help }}</small>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import VueTagsInput from '@johmun/vue-tags-input';
import utils from "../../config-utils";
import {reduxActions} from "../../store/develop-and-config";

export default {
    components: {
        VueTagsInput
    },
    props: {
        label: String,
        icon: String,
        help: String,
        dropdownWidth: {
            default: null,
            type: Number | null
        },
        items: Object | Array,
        actionType: Object,
        maxItems: {
            default: 10,
            type: Number
        },
        addOnlyFromAutocomplete: {
            default: true,
            type: Boolean
        },
        manualItems: {
            // z.B. [{label: 'Leere Zeichenkette', value: ''}, ...]
            default: () => [],
            type: Array
        },
        syntaxInclude: {
            default: () => [],
            type: Array
        },
        pipeInclude: {
            default: () => [],
            type: Array
        },
        editable: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            fetching: false,
            tag: '',
            selectedSyntaxCategory: null,
            selectedPipeCategory: null,
            selectedManualCategory: null,
            syntaxValues: {},
            pipeValues: {},
            debounce: null,
            omittedItems: false,
            edit: null,
            hiddenItem: {
                text: 'Hidden',
                value: '_empty',
                classes: 'hidden-autocomplete-item'
            }
        };
    },
    computed: {
        ...mapGetters(['ui']),
        tags() {
            return this.prepareTagsForComponent(this.items);
        },
        syntaxKeys() {
            return Object.keys(this.syntaxLoaderLabels()).filter(ele => this.syntaxInclude.includes(ele));
        },
        pipeKeys() {
            return Object.keys(this.pipeLoaderLabels()).filter(ele => this.pipeInclude.includes(ele));
        },
        manualKeys() {
            if (this.manualItems.length) {
                return Object.keys(this.manualLabels());
            }

            return [];
        },
        allKeys() {
            return [
                ...this.manualKeys,
                ...this.syntaxKeys,
                ...this.pipeKeys,
            ];
        },
        hasCategorySelected() {
            return this.selectedSyntaxCategory || this.selectedPipeCategory;
        },
        syntaxValuesArr() {
            // Wenn deaktiviert oder eine PipeCategory aktiv ist.
            if (this.selectedPipeCategory || this.selectedManualCategory) {
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
            if (this.selectedSyntaxCategory || this.selectedManualCategory) {
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
        manualValues() {
            if (this.selectedSyntaxCategory || this.selectedPipeCategory) {
                return [];
            }

            return this.manualItems;
        },
        autoCompleteItems() {
            let items = [
                ...this.manualValues,
                ...this.pipeValuesArr,
                ...this.syntaxValuesArr,
            ];

            items = items.map(function (ele) {
                ele.text = ele.label;

                return ele;
            });

            return [
                ...new Map(items.map(item => [
                    item['text'],
                    item
                ])).values()
            ];
        },
        emptyAutocompleteList() {
            return this.filteredItems.filter(ele => ele.value !== '_empty').length === 0;
        },
        filteredItems() {
            let that = this;
            let items = this.autoCompleteItems.filter(function (i) {
                if (that.selectedPipeCategory) {
                    return i.part === that.selectedPipeCategory;
                }
                if (that.selectedSyntaxCategory) {
                    return i.part === that.selectedSyntaxCategory;
                }

                return i;
            });

            return [
                ...items,
                this.hiddenItem
            ];
        },
        placeholderValue() {
            if (this.tags.length >= this.maxItems) {
                return '';
            }

            return this.addOnlyFromAutocomplete ? 'Suche...' : 'Suche / Eingabe...';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        resetAutocomplete() {
            this.clearCategory();
        },
        clearCategory() {
            this.syntaxValues = {};
            this.pipeValues = {};
            this.selectedSyntaxCategory = null;
            this.selectedPipeCategory = null;

            if (this.tag.length >= 2) {
                this.fetchItems(this.syntaxKeys, this.pipeKeys);
            }
        },
        setSyntaxCategory(key) {
            this.edit = null;
            this.selectedSyntaxCategory = key;
            this.selectedPipeCategory = null;
            this.selectedManualCategory = null;
            this.loadSyntaxAndPipeValuesForKey(key, null);
        },
        setPipeCategory(key) {
            this.edit = null;
            this.selectedPipeCategory = key;
            this.selectedSyntaxCategory = null;
            this.selectedManualCategory = null;
            this.loadSyntaxAndPipeValuesForKey(null, key);
        },
        setManualCategory(key) {
            this.selectedSyntaxCategory = null;
            this.selectedPipeCategory = null;
            this.selectedManualCategory = key;
        },
        tagsChanged(newItems) {
            let that = this;
            this.edit = null;

            let items = newItems.map(function (ele) {
                // Falls ein manueller Wert eingetragen wurde.
                if (!ele.hasOwnProperty('value')) {
                    ele.value = ele.text;
                    ele.label = ele.text;
                }

                if (ele.value.startsWith('[[') && ele.value.endsWith(']]')) {
                    let obj = that.getSyntaxParts(ele.value);

                    // Falls der Wert, z.B. "[[process.outputs.field_1]]" noch kein Label hat, wird dieses hinzugefügt.
                    // --> "[[process.outputs.field_1((Prozess-Daten - field_1))]]"
                    if (!obj.label) {
                        return ele.value.slice(0, -2) + '((' + ele.label + '))]]';
                    }
                }

                // Falls es eine Pipe-Notation ist.
                if (ele.value.includes('|')) {
                    let obj = that.getSyntaxParts(ele.value);

                    // Falls der Wert, z.B. "process|972bd0bb-be34-4991-85b7-6e04ae684696" noch kein Label hat, wird dieses hinzugefügt.
                    // --> "process|972bd0bb-be34-4991-85b7-6e04ae684696[Demo-Prozess]"
                    if (!obj.label) {
                        return ele.value + '[' + ele.label + ']';
                    }
                }

                return ele.value;
            });

            this.$emit('items-changed', items);
        },
        getTextFromValue(value) {
            let clearedValue = this.getSyntaxParts(value);
            let item = this.autoCompleteItems.find(ele => ele.value === clearedValue.syntax);

            return item ? item.text : clearedValue.label || clearedValue.syntax;
        },
        prepareTagsForComponent(items) {
            return (items || []).map(value => ({
                text: this.getTextFromValue(value),
                value: value
            }));
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
                search: this.tag.length >= 2 ? this.tag : null,
                action_type_id: this.actionType ? this.actionType.id : null,
                syntax_parts: syntaxParts,
                pipe_parts: pipeParts
            };

            that.fetching = true;

            this.debounce = setTimeout(() => {
                axios.post('/api/process-versions/' + that.ui.processVersionId + '/syntax-values', data).then(function (response) {
                    that.syntaxValues = response.data.syntax;
                    that.pipeValues = response.data.pipe;
                    that.omittedItems = response.data.omittedItems;
                    that.fetching = false;
                }).catch(function (error) {
                    that.fetching = false;
                    console.log(error);
                });
            }, 350);
        },
        addCustom() {
            let edit = {...this.edit};
            edit.value = edit.value.slice(0, -2) + '((' + edit.label + '))]]';

            this.tagsChanged([
                ...this.tags,
                edit
            ]);
        }
    },
    watch: {
        tag: {
            handler(newVal) {
                this.edit = null;

                // No category
                if (!this.hasCategorySelected) {
                    if (newVal.length >= 2) {
                        this.fetchItems(this.syntaxKeys, this.pipeKeys);
                    }
                    else {
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