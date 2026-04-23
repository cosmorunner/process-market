<template>
    <div class="form-group input-group-sm mb-2">
        <label class="mb-0">{{ label }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                    <span class="input-group-text">
                        <span class="material-icons">{{ icon }}</span>
                    </span>
            </div>
            <vue-tags-input v-model="tag" :tags="tags"
                            :autocomplete-items="autocompleteItems"
                            :add-only-from-autocomplete="true"
                            :disabled="error !== null"
                            :max-tags="1"
                            :autocomplete-min-length="0" :save-on-key="[13, ';']" placeholder=""
                            @tags-changed="tagsChanged"/>
        </div>
        <small class="text-danger" v-if="error">{{ error }}</small>
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
        icon: {
            default: 'local_offer',
            type: String
        },
        fullNamespace: String | null,
        help: {
            default: '',
            type: String
        },
        graphs: Array
    },
    data() {
        let autocompleteItems = [];
        let addedNamespaces = [];

        for (let i = 0; i < this.graphs.length; i++) {
            let namespace = this.graphs[i].namespace + '/' + this.graphs[i].identifier;

            if (!addedNamespaces.includes(namespace)) {
                autocompleteItems.push({
                    text: namespace,
                    value: namespace
                });

                addedNamespaces.push(namespace);
            }
        }

        return {
            error: null,
            tag: '',
            autocompleteItems: autocompleteItems,
            allAutocompleteItems: this.graphs.map(graph => (
                {
                    text: graph.namespace + '/' + graph.identifier,
                    value: graph.namespace + '/' + graph.identifier
                }
            ))
        };
    },
    computed: {
        tags() {
            if (this.fullNamespace) {
                return [
                    {
                        text: this.fullNamespace,
                        value: this.fullNamespace
                    }
                ];
            } else {
                return [];
            }
        }
    },
    watch: {
        tag: 'searchProcesses',
    },
    methods: {
        searchProcesses: function () {
            if (this.tag.length < 1) {
                this.autocompleteItems = this.allAutocompleteItems;
                return;
            }

            this.autocompleteItems = this.allAutocompleteItems.filter(ele => ele.text.toLowerCase().includes(this.tag));
        },
        tagsChanged(newItems) {
            this.$emit('item-changed', newItems.map(ele => ele.value)[0] || null);
        },
        getValueFromToOption(value) {
            let trimmed = value.replace(/[\[\]']+/g, '');

            if (trimmed.startsWith('action.outputs')) {
                return 'Aktions-Daten - ' + trimmed.split('.').pop();
            } else if (trimmed.startsWith('process.outputs')) {
                return 'Prozess-Daten - ' + trimmed.split('.').pop();
            } else if (value === 'process|identity') {
                return 'Prozess-Identität des Benutzers';
            } else {
                return value;
            }
        }
    }
};
</script>

