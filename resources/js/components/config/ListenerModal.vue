<template>
    <div class="modal-dialog modal-lg" role="document" v-if="listener">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">{{ addMode ? 'Listener anlegen' : 'Listener bearbeiten' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="description">Beschreibung</label>
                    <textarea id="description" class="form-control" name="description" maxlength="200"
                              :readonly="!ui.editable"
                              v-model="listener.description">{{listener.description}}</textarea>
                    <div v-for="error in (ui.validationErrors.description || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group input-group-sm mb-3">
                    <label class="mb-0">Events</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <span class="material-icons">flag</span>
                            </span>
                        </div>
                        <vue-tags-input v-model="tag" :tags="tags" :autocomplete-items="autoCompleteItems"
                                        :add-only-from-autocomplete="true" :max-tags="3" :disabled="!ui.editable"
                                        :autocomplete-min-length="0" :save-on-key="[13, ';']" placeholder=""
                                        @tags-changed="tagsChanged"/>
                    </div>
                    <div v-for="error in (ui.validationErrors.events || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group input-group-sm mb-3">
                    <AutocompleteSelector :items="listener.relation_type ? [listener.relation_type] : []"
                                          :label="'Verknüpfungstyp-Kondition'" :icon="'settings_ethernet'"
                                          :max-items="1" :pipe-include="['relation_types', 'graphs_relation_types']"
                                          :help="'Nur ausführen wenn das Event von einem verknüpften Prozess ausgelöst wurde.'"
                                          :editable="ui.editable" @items-changed="onChangeRelationType"/>
                </div>
                <div class="form-group">
                    <label class="mb-0">Weitere Konditionen</label>
                    <div class="mb-3">
                        <ConditionsTable :conditions="[...listener.conditions]" @delete-item="onDeleteCondition"
                                         :editable="ui.editable"/>
                    </div>
                    <ConditionsAdd :action-outputs="[]" :process-outputs="outputs"
                                   :conditions="[...listener.conditions]" v-if="ui.editable"
                                   @add-condition="onConditionAdd"/>
                </div>
                <hr/>
                <template v-if="listener.type === 'execute_action'">
                    <div class="form-group input-group-sm mb-2">
                        <label class="mb-0" for="action_type">Aktion</label>
                        <select class="form-control" id="action_type" name="action_type" @change="onChangeActionType"
                                :value="currentActionTypeId" :disabled="!ui.editable">
                            <option value="">Bitte wählen...</option>
                            <option v-for="actionType in action_types" :value="actionType.id">{{
                                    actionType.name
                                }}
                            </option>
                        </select>
                    </div>
                </template>
            </div>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import utils from '../../config-utils';
import {reduxActions} from "../../store/develop-and-config";
import OptionBadges from "../utils/OptionBadges";
import SortedRelationTypes from "../utils/SortedRelationTypes";
import ConditionsTable from "./partials/ConditionsTable";
import ConditionsAdd from "./partials/ConditionsAdd";
import VueTagsInput from "@johmun/vue-tags-input";
import AutocompleteSelector from "../utils/AutocompleteSelector";
import ModalFooter from "./ModalFooter";

export default {
    components: {
        ModalFooter,
        AutocompleteSelector,
        ConditionsAdd,
        ConditionsTable,
        SortedRelationTypes,
        OptionBadges,
        VueTagsInput
    },
    data() {
        return {
            listener: null,
            addMode: false,
            tag: '',
            tags: []
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
            'graphs_events',
            'action_types',
            'relation_types',
            'graphs',
            'graphs_relation_types',
            'process',
            'outputs',
            'events'
        ]),
        autoCompleteItems() {
            return [
                ...new Set([
                    ...this.graphs_events,
                    ...(this.listener.events || []),
                    ...this.events.map(ele => this.process.full_namespace + '::' + ele.name)
                ])
            ]
                .map(ele => ({
                    text: ele,
                    value: ele
                }))
                .filter(i => i.text.toLowerCase().indexOf((this.tag || '').toLowerCase()) !== -1)
                .sort();
        },
        currentActionTypeId() {
            return this.getSyntaxParts(this.listener.type_options.action_type).key;
        },
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreListener' : 'UpdateListener';
            this.patchDefinition(method, this.listener).then(this.closeModal).catch(() => {
            });
        },
        tagsChanged(newItems) {
            this.listener.events = newItems.map(ele => ele.value);
        },
        onChangeActionType(e) {
            let actionType = this.action_types.find(ele => ele.id === e.target.value);

            if (!e.target.value || !actionType) {
                this.listener.type_options = {
                    ...this.listener.type_options,
                    action_type: ''
                };
            }
            this.listener.type_options = {
                ...this.listener.type_options,
                action_type: this.setSyntaxLabel('actionType|' + actionType.id, 'Aktion - ' + actionType.name)
            };
        },
        onChangeRelationType(autocompleteItems) {
            if (!autocompleteItems.length) {
                this.listener = {
                    ...this.listener,
                    relation_type: null
                };

                return;
            }

            let parts = this.getSyntaxParts(autocompleteItems[0]);

            // Erst in external schauen
            let relationType = this.graphs_relation_types.find(ele => ele.namespace === parts.namespace && ele.reference === parts.key);

            // Wenn existiert dann Namespace aus parts übernehmen
            if (!relationType) {
                relationType = this.relation_types.find(ele => ele.reference === parts.key);
            }

            if (relationType) {
                this.listener = {
                    ...this.listener,
                    relation_type: this.setSyntaxLabel(parts.syntax, parts.label)
                };
            }
            else {
                this.listener = {
                    ...this.listener,
                    relation_type: null
                };
            }
        },
        onConditionAdd(condition) {
            this.listener = {
                ...this.listener,
                conditions: [
                    ...this.listener.conditions,
                    condition
                ]
            };
        },
        onDeleteCondition(item) {
            let conditions = [...this.listener.conditions].filter(function (ele) {
                return JSON.stringify(ele) !== JSON.stringify(item);
            });

            this.listener = {
                ...this.listener,
                conditions: [...conditions]
            };
        }
    },
    mounted() {
        if (this.ui.modal.data.listener) {
            this.listener = {...this.ui.modal.data.listener};
        }
        else {
            this.addMode = true;
            this.listener = {
                description: '',
                events: [],
                relation_type: null,
                conditions: [],
                type: 'execute_action',
                type_options: {
                    action_type: '',
                    mapping: {}
                }
            };
        }

        // Event Tags initialisieren
        this.tags = this.listener.events.map(value => ({
            text: value,
            value: value
        }));

        if (!this.graphs.length) {
            this.fetchUserGraphs();
        }
    },

};
</script>
