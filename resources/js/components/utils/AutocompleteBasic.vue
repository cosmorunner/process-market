<template>
    <div class="form-group input-group-sm mb-0">
        <label class="mb-0" v-if="label">{{ label }}</label>
        <div class="input-group">
            <div class="input-group-prepend" v-if="icon">
                    <span class="input-group-text">
                        <span class="material-icons">{{ icon }}</span>
                    </span>
            </div>
            <vue-tags-input v-model="tag" :tags="tags" :autocomplete-items="filteredItems"
                            :add-only-from-autocomplete="addOnlyFromAutocomplete" :max-tags="maxItems"
                            :autocomplete-min-length="0" :save-on-key="[13, ';']" :placeholder="placeholderValue"
                            @tags-changed="tagsChanged" @focus="onFocus">
                <div slot="autocomplete-header" v-if="httpItemsLoaded">
                    <div v-if="!addOnlyFromAutocomplete" class="px-2 pt-2">
                        <small class="text-muted">Eigenen Wert mit Eingabe-Taste bestätigen.</small>
                    </div>
                </div>
                <div slot="autocomplete-header" class="px-2 pt-2 pb-1" v-if="fetching">
                    <img src="/img/loading.gif" alt="Loading" width="14.4" height="14.4"/>
                </div>
                <div slot="autocomplete-footer" style="padding: 3px 6px;"
                     v-if="emptyAutocompleteList && httpItemsLoaded && !fetching">
                    <span class="text-muted">Keine Einträge...</span>
                </div>
            </vue-tags-input>
        </div>
        <small class="text-muted" v-if="help">{{ help }}</small>
    </div>
</template>

<script>

import VueTagsInput from '@johmun/vue-tags-input';

export default {
    components: {
        VueTagsInput
    },
    props: {
        label: String,
        icon: String,
        help: String,
        httpEndpoint: String,
        httpItemsMappingFunction: Function,
        dropdownWidth: {
            default: null,
            type: Number | null
        },
        items: Object | Array,
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
        excludeItems: {
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
            httpItemsLoaded: false,
            httpItems: [],
            fetching: false,
            tag: '',
            selectedManualCategory: null
        };
    },
    computed: {
        tags() {
            return this.prepareTagsForComponent(this.items);
        },
        manualValues() {
            return this.manualItems;
        },
        autoCompleteItems() {
            return [
                ...this.manualValues,
                ...this.httpItems
            ].filter(ele => !this.excludeItems.includes(ele.value));
        },
        emptyAutocompleteList() {
            return this.filteredItems.filter(ele => ele.value !== '_empty').length === 0;
        },
        filteredItems() {
            // Tag-Suche
            if (this.tag.length >= 2) {
                // Array an Such-Worter mit Länge >= 2
                let words = this.tag.split(' ')
                    .map(ele => ele.trim())
                    .filter(ele => ele.length >= 2)
                    .map(ele => ele.toLowerCase());

                let items = this.autoCompleteItems.filter(function (i) {
                    return words.reduce((carry, ele) => carry && i.text.toLowerCase().indexOf(ele) !== -1, true);
                });

                // Damit das Dropdown sich nicht schließt wenn es keine Einträge anzuzeigen gibt.
                return [
                    ...items,
                    {
                        text: 'Hidden',
                        value: '_empty',
                        classes: 'hidden-autocomplete-item'
                    }
                ];
            }

            return [
                ...this.autoCompleteItems,
                {
                    text: 'Hidden',
                    value: '_empty',
                    classes: 'hidden-autocomplete-item'
                }
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
        tagsChanged(newItems) {
            let items = newItems.map(function (ele) {
                // Falls ein manueller Wert eingetragen wurde.
                if (!ele.hasOwnProperty('value')) {
                    ele.value = ele.text;
                    ele.label = ele.text;
                }

                return ele.value;
            });

            this.$emit('items-changed', items);
        },
        prepareTagsForComponent(items) {
            return (items || []).map(value => ({
                text: value,
                value: value
            }));
        },
        onFocus() {
            if (this.httpItems.length) {
                return;
            }

            this.fetching = true;
            let that = this;

            axios.get(this.httpEndpoint).then(function (response) {
                that.httpItems = that.httpItemsMappingFunction(response.data);
                that.fetching = false;
            }).catch(function (error) {
                that.fetching = false;
                console.log(error);
            });
        }
    }
};
</script>

