<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ModalHeader class="text-actionrule" :title="addMode ? (statusType.name + ' - Neue Aktionsregel') : (statusType.name + ' - Aktionsregel bearbeiten')"
                         docs-article="actionrules-statusrules"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form v-if="action_types.length">
                            <div class="form-group mb-2" v-if="action_types.length">
                                <label for="action_type_id">Aktion</label>
                                <template v-if="addMode">
                                    <select class="form-control form-control-sm" id="action_type_id"
                                            v-model="data.action_type_id">
                                        <option :value="actionType.id" v-for="actionType in sortedActionTypes"
                                                :disabled="actionType.action_rules.find(ele => ele.status_type_id === statusType.id)">
                                            {{ actionType.name }}
                                            {{
                                                actionType.action_rules.find(ele => ele.status_type_id === statusType.id) ? '(Regel existiert bereits)' : ''
                                            }}
                                        </option>
                                    </select>
                                </template>
                                <template v-else>
                                    <span class="d-block rounded py-1 px-2 bg-light border">{{
                                            selectedActionType.name
                                        }}</span>
                                </template>
                                <div v-for="error in (ui.validationErrors.action_type_id || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="d-block w-100 mb-2">Regel</span>
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">
                                            <span v-if="statusType.image"
                                                  class="material-icons mr-1">{{ statusType.image }}</span>
                                            {{ statusType.name }}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="operator" v-model="data.operator">
                                        <option value="IN_ARRAY">ist gleich</option>
                                        <option value="NOT_IN_ARRAY">ist nicht gleich</option>
                                        <option value="LOWER">ist kleiner als</option>
                                        <option value="LOWER_OR_EQUAL">ist kleiner oder gleich</option>
                                        <option value="GREATER">ist größer als</option>
                                        <option value="GREATER_OR_EQUAL">ist größer oder gleich</option>
                                        <option value="IN_BETWEEN">ist zwischen</option>
                                    </select>
                                </div>
                                <div v-for="error in (ui.validationErrors.operator || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="manualValues"
                                           :checked="manualValues" @click="switchManualValues">
                                    <label class="form-check-label" for="manualValues">Manuelle Werte</label>
                                </div>
                            </div>
                            <div class="form-group mb-2" v-if="!manualValues && statusType.states.length">
                                <label>Zustände</label>
                                <vue-tags-input v-model="tag" :tags="tags" :required="true"
                                                :autocomplete-items="autocompleteItems"
                                                :autocomplete-min-length="0" :add-only-from-autocomplete="true"
                                                :placeholder="''" @tags-changed="updateAutoComplete"
                                                :save-on-key="[13, ';']"/>
                                <div v-for="error in (ui.validationErrors.state_ids || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="alert alert-info p-2" role="alert" v-if="!statusType.states.length">
                                Fügen Sie zunächst Zustände zum Status hinzu. Rechtklick > "Neuer Zustand" auf den
                                Status im Prozess-Graphen.
                            </div>
                            <div class="form-group mb-2" v-if="manualValues">
                                <label>Werte</label>
                                <vue-tags-input v-model="tag" :tags="tags" :required="false"
                                                :autocomplete-min-length="1" :add-only-from-autocomplete="false"
                                                :placeholder="''" @tags-changed="updateAutoComplete"
                                                :save-on-key="[13, ';']"/>
                                <small class="text-muted">Wert mit Eingabetaste bestätigen.</small>
                                <div v-for="error in (ui.validationErrors.values || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="group">Gruppe</label>
                                <template>
                                    <select class="form-control form-control-sm" id="group" v-model="data.group">
                                        <option value="group_1">Gruppe 1</option>
                                        <option value="group_2">Gruppe 2</option>
                                        <option value="group_3">Gruppe 3</option>
                                        <option value="group_4">Gruppe 4</option>
                                        <option value="group_5">Gruppe 5</option>
                                        <option value="group_6">Gruppe 6</option>
                                        <option value="group_7">Gruppe 7</option>
                                        <option value="group_8">Gruppe 8</option>
                                        <option value="group_9">Gruppe 9</option>
                                        <option value="group_10">Gruppe 10</option>
                                    </select>
                                </template>
                                <div v-for="error in (ui.validationErrors.group || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                                <small class="text-muted">Aktionsregeln in der selben Gruppe werden mit UND verknüpft. Gruppen untereinander Werden mit ODER verknüpft.</small>
                            </div>
                        </form>
                        <div class="alert alert-info p-2" role="alert" v-if="!action_types.length">
                            Legen Sie zunächst eine Aktion an mit Rechtsklick "Neue Aktion" auf dem Prozess-Graphen.
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" v-on="$listeners" :on-save="onSave"
                         :save-label="addMode ? 'Hinzufügen' : 'Bearbeiten'"
                         :save-disabled="!action_types.length || !statusType.states.length"
            />
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import VueTagsInput from '@johmun/vue-tags-input';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        ModalHeader,
        VueTagsInput,
        ModalFooter
    },
    data() {
        return {
            data: {
                id: '',
                action_type_id: '',
                operator: 'IN_ARRAY',
                values: [],
                state_ids: [],
                group: 'group_1'
            },
            tag: '',
            tags: [],
            manualValues: false
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'status_types'
        ]),
        addMode() {
            return this.data.id === '';
        },
        selectedActionType() {
            return this.action_types.find(ele => ele.id === this.data.action_type_id);
        },
        statusType() {
            return this.status_types.find(ele => ele.id === this.ui.modal.data.statusTypeId);
        },
        autocompleteItems() {
            return this.statusType.states.map(ele => ({
                text: ele.description,
                value: ele.id
            }));
        },
        sortedActionTypes() {
            return [...this.action_types].sort((a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()));
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let that = this;
            let position = JSON.stringify(this.ui.modal.data.position);

            // Prüfen ob bereits eine ActionNode in dem Status existiert (z.B. durch Statusregel). Falls ja muss die
            // Position nicht gesetzt werden.
            let existingActionNode = cy.nodes().filter(ele => ele.data('model_id') === this.data.action_type_id && ele.data('status_type_id') === this.statusType.id)[0] || null;

            if (existingActionNode) {
                position = JSON.stringify(existingActionNode.position);
            }

            let data = {
                ...this.data,
                status_type_id: this.statusType.id
            };

            if (position) {
                data.position = position;
            }

            let method = this.addMode ? 'StoreActionRule' : 'UpdateActionRule';

            this.patchDefinition(method, data).then(that.closeModal).catch(() => {
            });
        },
        updateAutoComplete(newTags) {
            if (this.manualValues) {
                this.data = {
                    ...this.data,
                    values: newTags.map(ele => ele.text),
                    state_ids: []
                };
            }

            if (!this.manualValues) {
                this.data = {
                    ...this.data,
                    values: [],
                    state_ids: newTags.map(ele => ele.value)
                };
            }
        },
        switchManualValues() {
            this.manualValues = !this.manualValues;
            this.tags = [];
            this.data = {
                ...this.data,
                values: [],
                state_ids: []
            };
        }
    },
    watch: {
        data: {
            handler: function (val, oldVal) {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    mounted() {
        if (this.ui.modal.data.actionTypeId) {
            this.data.action_type_id = this.ui.modal.data.actionTypeId;

            // Aktionsregel bearbeiten
            if (this.ui.modal.data.actionRuleId) {
                let actionRule = this.selectedActionType.action_rules.find(ele => ele.id === this.ui.modal.data.actionRuleId);
                let that = this;

                this.data = {
                    ...this.data,
                    ...actionRule,
                    action_type_id: this.ui.modal.data.actionTypeId
                };

                // Manuelle Werte Anzeige
                if(actionRule.values.length) {
                    this.manualValues = true;
                }

                // Tags für Autocomplete erstellen
                if (actionRule.state_ids.length) {
                    this.tags = actionRule.state_ids.map(function (stateId) {
                        let state = that.statusType.states.find(ele => ele.id === stateId);

                        if (!state) {
                            return null;
                        }

                        return {
                            text: state.description,
                            value: state.id
                        };
                    }).filter(ele => ele !== null);
                }

                if(actionRule.values.length) {
                    this.tags = actionRule.values.map(ele => ({
                        text: ele,
                        value: ele
                    }))
                }

            }
        }
        else {
            this.data.action_type_id = '';
        }
    }
};
</script>
