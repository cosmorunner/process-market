<template>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <ModalHeader class="text-statusrule" :title="addMode ? (statusType.name + ' - Neue Statusregel') : (statusType.name + ' - Statusregel bearbeiten')"
                         docs-article="actionrules-statusrules"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form v-if="action_types.length && !isSmartStatusType">
                            <div class="form-group mb-2" v-if="action_types.length">
                                <label for="action_type_id">Aktion</label>
                                <template v-if="addMode">
                                    <select class="form-control form-control-sm" id="action_type_id"
                                            @change="onActionTypeChange" v-model="data.action_type_id">
                                        <option value="">Bitte wählen...</option>
                                        <option :value="actionType.id" v-for="actionType in sortedActionTypes"
                                                :disabled="actionType.status_rules.find(ele => ele.status_type_id === statusType.id)">
                                            {{ actionType.name }} {{
                                                actionType.status_rules.find(ele => ele.status_type_id === statusType.id) ? '(Regel existiert bereits)' : ''
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
                                        <option value="SET">setzen auf</option>
                                        <option value="ADD">addieren</option>
                                        <option value="SUB">subtrahieren</option>
                                    </select>
                                </div>
                                <div v-for="error in (ui.validationErrors.operator || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="state" id="state" value="state"
                                           @click="switchValueSource('state')" :checked="valueSource === 'state'">
                                    <label class="form-check-label" for="state">Zustand</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="output" id="output-value"
                                           :disabled="!data.action_type_id" value="output"
                                           @click="switchValueSource('output')"
                                           :checked="valueSource === 'output' && data.action_type_id">
                                    <label class="form-check-label" for="output-value">Aktions-Datenfeld</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="manual" id="manual"
                                           value="manual" @click="switchValueSource('manual')"
                                           :checked="valueSource === 'manual'">
                                    <label class="form-check-label" for="manual">Manueller Wert</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="conditions" id="conditions"
                                           :disabled="!data.action_type_id" value="conditions"
                                           @click="switchValueSource('conditions')"
                                           :checked="valueSource === 'conditions'">
                                    <label class="form-check-label" for="conditions">Konditionen</label>
                                </div>
                            </div>
                            <div class="form-group mb-2" v-if="valueSource === 'state'">
                                <div class="alert alert-info p-2" role="alert" v-if="!statusType.states.length">
                                    Fügen Sie zunächst Zustände zum Status hinzu. Bei "Regeln & Daten": Rechtsklick auf
                                    den Status -> "Neuer Zustand".
                                </div>
                                <select class="form-control form-control-sm" id="state-value" v-model="data.state">
                                    <option value="">Bitte wählen...</option>
                                    <option :value="state.id" v-for="state in statesWithoutRange">
                                        {{ state.description }}
                                    </option>
                                </select>
                                <small class="text-muted">Es können nur Zustände ohne Wertebereich gewählt
                                    werden.</small>
                                <div v-for="error in (ui.validationErrors.values || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2" v-if="valueSource === 'manual'">
                                <input type="text" class="form-control form-control-sm" id="values"
                                       :value="data.values[0]" aria-describedby="values" @input="onInputManual"
                                       placeholder="z.B. 1, 2.5 oder 1.337" required/>
                                <div v-for="error in (ui.validationErrors.values || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2" v-if="valueSource === 'output' && data.action_type_id">
                                <div class="alert alert-info p-2" role="alert"
                                     v-if="!selectedActionType.outputs.length">
                                    Fügen Sie zunächst Daten zum Aktionstyp hinzu.
                                </div>
                                <AutocompleteSelector :items="data.output ? [data.output] : []" :max-items="1"
                                                      :syntax-include="['action.outputs']"
                                                      @items-changed="$event.length ? data.output = $event[0] : data.output = ''"/>
                                <div v-for="error in (ui.validationErrors.output || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2" v-if="valueSource === 'conditions'">
                                <div class="row mb-3" v-for="(conditions, stateIdOrValue) in conditionsByStateIdOrValue">
                                    <div class="col-2">
                                        <span class="pl-2">
                                            <!-- Zustand -->
                                            <span class="badge badge-pill text-muted badge-light"
                                                  style="font-size: 90%;" v-if="stateIdOrValue.length === 36">
                                                <span class="material-icons"
                                                      :style="'color:' + state(stateIdOrValue).color">{{
                                                        state(stateIdOrValue).image
                                                    }}</span>
                                                {{ stateDescription(stateIdOrValue) }}
                                            </span>
                                            <span v-else>
                                                {{ stateIdOrValue }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-1">
                                        <span>wenn</span>
                                    </div>
                                    <div class="col-9 mb-2">
                                        <div class="mb-2">
                                            <ConditionsTable :conditions="castedConditions(conditions)" @delete-item="deleteCondition($event, stateIdOrValue)"/>
                                        </div>
                                        <ConditionsAdd
                                            :syntax-loader-include="['action.outputs', 'process.outputs', 'process.status', 'reference.outputs', 'reference.relation_data', 'auth.identity.outputs', 'auth.identity.status']"
                                            :conditions="data.conditions || []"
                                            @add-condition="onConditionAdd($event, stateIdOrValue)"/>
                                    </div>
                                </div>
                                <hr v-if="data.conditions.length" class="mb-3"/>
                                <div class="row">
                                    <div class="col-2 pr-0">
                                        <div class="input-group input-group-sm rounded-left">
                                            <div :class="'input-group-append'">
                                                <button class="btn btn-outline-primary dropdown-toggle text-truncate"
                                                        type="button" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                    <span>{{
                                                            newCondition[0].length === 36 ? stateDescription(newCondition[0]) : '#'
                                                        }}</span>
                                                </button>
                                                <div class="dropdown-menu scrollable-dropdown">
                                                    <button type="button" class="dropdown-item"
                                                            @click="changeNewCondition('', 0)">#
                                                    </button>
                                                    <button type="button" class="dropdown-item"
                                                            @click="changeNewCondition(state.id, 0)"
                                                            v-for="state in unusedStatesForConditions">
                                                        {{ state.description }}
                                                    </button>
                                                </div>
                                            </div>
                                            <input v-if="newCondition[0].length !== 36" type="number" step=".001"
                                                   class="form-control form-control-sm" placeholder="Wert..."
                                                   :value="newCondition[0]" :data-index="0"
                                                   @input="changeNewCondition($event.target.value, 0)" maxlength="13">
                                        </div>
                                    </div>
                                    <div class="col-1 pr-0">
                                        <span>wenn</span>
                                    </div>
                                    <div class="col-9">
                                        <ConditionsAdd
                                            v-if="newCondition[0].trim()"
                                            :syntax-loader-include="['action.outputs', 'process.outputs', 'process.status', 'reference.outputs', 'reference.relation_data', 'auth.identity.outputs', 'auth.identity.status']"
                                            :conditions="data.conditions || []" @add-condition="onConditionAdd($event, newCondition[0])"/>
                                    </div>
                                </div>
                                <div v-for="error in (ui.validationErrors.values || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                        </form>
                        <div class="alert alert-info p-2" role="alert" v-if="!action_types.length">
                            Legen Sie zunächst eine Aktion an mit Rechtsklick "Neue Aktion" auf dem Prozess-Graphen.
                        </div>
                        <div class="alert alert-info p-2" role="alert" v-if="isSmartStatusType">
                            Es kann keine Statusregel für einen Smart-Status erstellt werden. Ein Smart-Status berechnet
                            den aktuellen Wert automatisch.
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter :save-disabled="action_types.length === 0 || isSmartStatusType" :ui="ui" v-on="$listeners"
                         :on-save="onSave" :save-label="addMode ? 'Hinzufügen' : 'Speichern'"/>
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
import OptionBadges from "../utils/OptionBadges";
import DropdownSelector from "../utils/DropdownSelector";
import AutocompleteSelector from "../utils/AutocompleteSelector";
import ConditionsAdd from "../config/partials/ConditionsAdd.vue";
import ConditionsTable from "../config/partials/ConditionsTable.vue";

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        ConditionsTable,
        ConditionsAdd,
        AutocompleteSelector,
        DropdownSelector,
        OptionBadges,
        ModalHeader,
        VueTagsInput,
        ModalFooter
    },
    data() {
        return {
            data: {
                id: '',
                action_type_id: '',
                operator: 'SET',
                values: [],
                output: '',
                state: '',
                conditions: []
            },
            tag: '',
            tags: [],
            valueSource: 'state',
            newCondition: [
                '',
                '',
                '=',
                ''
            ],
            operatorLabels: {
                '=': 'Gleich',
                '!=': 'Nicht gleich',
                '<': 'Kleiner als',
                '<=': 'Kleiner oder gleich',
                '>=': 'Größer oder gleich',
                '>': 'Größer als'
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'status_types',
            'definition'
        ]),
        addMode() {
            return this.data.id === '';
        },
        statusType() {
            return this.status_types.find(ele => ele.id === this.ui.modal.data.statusTypeId);
        },
        selectedActionType() {
            return this.action_types.find(ele => ele.id === this.data.action_type_id);
        },
        statesWithoutRange() {
            let statusType = this.statusType;

            if (!statusType) {
                return [];
            }

            return statusType.states.filter(ele => ele.min === ele.max);
        },
        unusedStatesForConditions(){
            let states = this.statesWithoutRange;
            let conditionStateIds = Object.keys(this.conditionsByStateIdOrValue).filter(ele => ele.length === 36)

            return states.filter(ele => !conditionStateIds.includes(ele.id))
        },
        isSmartStatusType() {
            if (this.statusType) {
                return Object.keys(this.statusType.smart || {}).length > 0;
            }

            return false;
        },
        conditionsByStateIdOrValue() {
            let grouped = {};

            for (let i = 0; i < this.data.conditions.length; i++) {
                let stateId = this.data.conditions[i][0];

                if (grouped.hasOwnProperty(stateId)) {
                    grouped[stateId].push(this.data.conditions[i]);
                } else {
                    grouped[stateId] = [this.data.conditions[i]];
                }
            }

            // Nach Gruppen-Nummer sortieren
            grouped = Object.keys(grouped).sort().reduce((obj, key) => ({
                ...obj,
                [key]: grouped[key]
            }), {});

            return grouped;
        },
        sortedActionTypes() {
            return [...this.action_types].sort((a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()));
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let position = JSON.stringify(this.ui.modal.data.position);

            // Prüfen ob bereits eine ActionNode in dem Status existiert (z.B. durch Statusregel). Falls ja muss die
            // Position nicht gesetzt werden.
            let existingActionNode = cy.nodes().filter(ele => ele.data('model_id') === this.data.action_type_id && ele.data('status_type_id') === this.statusType.id)[0] || null;

            if (existingActionNode && this.data.operator === 'SET') {
                position = JSON.stringify(existingActionNode.position());
            }

            let data = {
                ...this.data,
                status_type_id: this.statusType.id,
            };

            if (this.valueSource === 'output') {
                delete data.values;
                delete data.state;
                delete data.conditions;
            }

            if (this.valueSource === 'state') {
                delete data.values;
                delete data.output;
                delete data.conditions;
            }

            if (this.valueSource === 'manual') {
                delete data.output;
                delete data.state;
                delete data.conditions;
            }

            if (this.valueSource === 'conditions') {
                delete data.values;
                delete data.state;
                delete data.output;
            }

            if (position) {
                data.position = position;
            }

            let method = this.addMode ? 'StoreStatusRule' : 'UpdateStatusRule';

            this.patchDefinition(method, data, true).then(this.closeModal).catch(() => {
            });
        },
        onActionTypeChange(e) {
            this.data = {
                ...this.data,
                action_type_id: e.target.value,
                values: [],
                output: '',
                state: '',
                conditions: []
            };
        },
        onInputManual(e) {
            this.data = {
                ...this.data,
                values: [e.target.value]
            };
        },
        switchValueSource(valueSource) {
            this.valueSource = valueSource;
            this.tags = [];
            this.data = {
                ...this.data,
                values: [],
                output: '',
                state: '',
                conditions: []
            };
        },
        changeNewCondition(value, index) {
            let newCondition = [...this.newCondition];

            newCondition[index] = value;

            this.newCondition = [...newCondition];
        },
        castedConditions(conditions) {
            return conditions.map(function (ele) {
                // Remove first element, because it holds the state-id/numeric value
                let condition = [...ele];
                condition.shift();

                return condition;
            });
        },
        onConditionAdd(condition, stateId) {
            if (condition[0].trim() === '') {
                return;
            }

            this.data.conditions = [
                ...this.data.conditions,
                [
                    stateId,
                    ...condition
                ]
            ];

            this.newCondition = [
                '',
                '',
                '=',
                ''
            ];
        },
        deleteCondition(item, stateIdOrValue) {
            item = [stateIdOrValue, ...item]

            this.data.conditions = [...this.data.conditions].filter(function (ele) {
                return JSON.stringify(ele) !== JSON.stringify(item);
            });
        },
        state(id) {
            let state = this.statusType.states.find(ele => ele.id === id);

            if (!state) {
                return {
                    color: '#013370',
                    description: 'Unknown',
                    image: 'help_outline',
                    min: '?',
                    max: '?'
                };
            }

            return state;
        },
        stateDescription(id) {
            let state = this.state(id);

            return this.state(id).description.length > 20 ? state.description.substr(0, 20) + '...' : state.description;
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

            // Statusregel bearbeiten
            if (this.ui.modal.data.statusRuleId) {
                let statusRule = this.selectedActionType.status_rules.find(ele => ele.id === this.ui.modal.data.statusRuleId);

                this.data = {
                    ...this.data, ...statusRule,
                    action_type_id: this.ui.modal.data.actionTypeId
                };

                // Aktions-Output
                if (statusRule.output && statusRule.output.length) {
                    this.valueSource = 'output';
                }
                // Zustände
                if (statusRule.state && statusRule.state.length) {
                    this.valueSource = 'state';
                }
                // Manueller Wert
                if (statusRule.values.length) {
                    this.valueSource = 'manual';
                }
                // Manueller Wert
                if (statusRule.conditions.length) {
                    this.valueSource = 'conditions';
                }
            }
        }
        else {
            this.data.action_type_id = '';
        }
    }
};
</script>
