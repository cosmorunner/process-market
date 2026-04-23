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
                            :autocomplete-items="autoCompleteItems"
                            :add-only-from-autocomplete="true"
                            :max-tags="maxItems"
                            :autocomplete-min-length="0" :save-on-key="[13, ';']" placeholder=""
                            @tags-changed="tagsChanged"/>
        </div>
        <small class="text-muted" v-if="help">{{ help }}</small>
    </div>
</template>

<script>

import {mapGetters} from 'vuex';
import VueTagsInput from '@johmun/vue-tags-input';
import utils from "../../../config-utils";

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
        items: Object | Array,
        outputs: Object | Array,
        actionType: Object,
        environmentGroups: {
            default: function () {
                return [];
            },
            type: Object | Array
        },
        processors: {
            default: function () {
                return [];
            },
            type: Object | Array
        },
        help: {
            default: '',
            type: String
        },
        maxItems: {
            default: 10,
            type: Number
        },
        syntaxLoaderInclude: {
            default: function () {
                return [];
            },
            type: Array
        },
        additionalAutoCompleteItems: {
            default: function () {
                return [];
            },
            type: Array
        },
        withRelationTypes: {
            default: false,
            type: Boolean
        },
        onlyFromActionType: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            tag: '',
            tags: [],
            modelNameToProperty: {
                role: 'roles',
                processor: 'processors',
                listConfig: 'list_configs',
                relationType: 'relation_types'
            },
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'environments',
            'process',
            'roles',
            'relation_types_with_single_process',
            'graphs_relation_types',
            'relation_types',
            'graphs_output_names',
            'graphs_status_types',
        ]),
        autoCompleteItems: function () {
            // Prozessoren
            let items = [
                ...this.processors.map(ele => ({
                    label: 'Ergebnis von "' + this.processorNames(ele.identifier) + '" - ID: ' + ele.id.substring(0, 4),
                    value: 'processor|' + ele.id,
                }))
            ];

            let relationTypes = []
            // Verknüpfungstypen
            if(this.withRelationTypes) {
                relationTypes = this.relation_types.map(ele => ({
                    label: 'Verknüpfungstyp - ' + ele.name,
                    value: 'relationType|' + ele.id
                }))
            }

            items = [
                ...this.additionalAutoCompleteItems,
                ...items,
                ...relationTypes,
                ...this.syntaxLoaderValues(this.definition, this.syntaxLoaderInclude, this.onlyFromActionType ? this.actionType.id : null)
            ];

            items = items.map(function (ele) {
                ele.text = ele.label;

                return ele;
            });

            return items.filter(i => {
                return i.label.toLowerCase().indexOf((this.tag || '').toLowerCase()) !== -1;
            });
        }
    },
    methods: {
        ...utils,
        tagsChanged(newItems) {
            this.$emit('items-changed', newItems.map(ele => ele.value));
        },
        getTextFromValue(value) {
            let trimmed = value.replace(/[\[\]']+/g, '');

            // Aktions-Daten
            if (trimmed.startsWith('action.outputs')) {
                return 'Aktions-Daten - ' + trimmed.split('.').pop();
            }

            // Prozess-Daten
            else if (trimmed.startsWith('process.outputs')) {
                return 'Prozess-Daten - ' + trimmed.split('.').pop();
            }

            // Prozess-Referenz Output-Daten
            else if (trimmed.startsWith('process.references') && trimmed.split('.')[3] === 'outputs') {
                let reference = trimmed.split('.')[2];

                return '[' + reference + '] - ' + 'Prozess-Daten - ' + trimmed.split('.').pop();
            }

            // App-Model
            else if (trimmed.includes('app::') && trimmed.includes('|')) {
                let parts = trimmed.split('::');
                let modelName = parts[1].split('|')[0];
                let identifier = parts[1].split('|')[1];

                let name = {
                    group: 'Gruppe',
                    connector: 'Connector',
                    request: 'Request',
                    role: 'System-Rolle',
                    user: 'Benutzer'
                }[modelName] || modelName;

                return name + ' - ' + identifier;
            }
            // Prozesstyp PipeLoader-Syntax
            else if (trimmed.includes('::') && trimmed.includes('|')) {
                let parts = trimmed.split('::');
                let modelName = parts[1].split('|')[0];
                let id = parts[1].split('|')[1];
                let model = this[this.modelNameToProperty[modelName]].find(ele => ele.id === id);

                if (modelName === 'processor' && model) {
                    return 'Ergebnis von "' + this.processorNames(model.identifier) + '" - ID: ' + model.id.substring(0, 4);
                }

                if (modelName === 'role' && model) {
                    return 'Rolle - ' + model.name;
                }

                return trimmed;
            }
            else {
                return value;
            }
        }
    },
    mounted() {
        this.tags = this.items.map(value => ({
            text: this.getTextFromValue(value),
            value: value
        }));
    }
};
</script>

