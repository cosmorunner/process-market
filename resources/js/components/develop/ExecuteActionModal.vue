<template>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <ModalHeader :title="actionType.name + ' - Aktions-Daten'"/>
            <div class="modal-body py-0">
                <div class="row">
                    <div :class="'py-2 ' + (showSideInfo ? 'col-7' : 'col-12')">
                        <div class="border-bottom">
                            <div class="input-group input-group-sm mb-3 mt-2">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="demoData">Demo-Werte setzen</label>
                                </div>
                                <select class="custom-select" id="demoData" @change="loadDemoData"
                                        v-model="selectedDemoData">
                                    <option selected value="">Keine Demo-Werte</option>
                                    <option :value="item.name" v-for="item in actionTypeDemoData">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <div class="input-group-append" v-if="selectedDemoData !== ''">
                                    <button class="btn btn-sm" @click="deleteDemoData">
                                        <span class="material-icons text-muted">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block my-2">
                            Ein Vorlade-Wert wird nur geladen, wenn der Vorlade-Daten-Typ mit dem gleichnamigen
                            Aktions-Daten-Typ übereinstimmt.
                        </small>
                        <hr/>
                        <span class="d-block my-2">
                            <span>Daten</span>
                            <button class="btn btn-sm px-1 p-0 btn-light" @click="resetData">
                                <small>Zurücksetzen</small>
                            </button>
                        </span>
                        <div class="row d-flex mb-2" v-for="output in actionType.outputs">
                            <div class="col-12">
                                <div class="row d-flex justify-content-end mb-0">
                                    <div class="col-10 pl-1 py-0 pr-2">
                                        <label class="mb-0">
                                            <span>{{ output.name }}</span>
                                            <span class="text-muted" v-if="output.description">
                                            - {{ output.description }}
                                            </span>
                                            <sup v-if="output.validation.includes('required')"
                                                 class="text-danger">*</sup>
                                        </label>
                                    </div>
                                </div>
                                <div class="row d-flex">
                                    <div class="col-2 p-1 align-self-start">
                                        <div class="custom-control custom-switch float-right">
                                            <input type="checkbox" class="custom-control-input"
                                                   :id="'switch' + output.name"
                                                   :checked="!disabled.includes(output.name)"
                                                   @click="toggleDisable(output.name)">
                                            <label class="custom-control-label" :for="'switch' + output.name"></label>
                                        </div>
                                    </div>
                                    <div class="col-10 px-0 pr-2">
                                        <template>
                                            <template v-if="output.type === 'array'"
                                                      v-for="(item, index) in data[output.name] || []">
                                                <div class="mb-1">
                                                    <ExecuteActionModalOutputValue :output="output" :output-value="item"
                                                                                   :errors="ui.validationErrors[output.name + '.' + index]"
                                                                                   :demo-user-identity-id="ui.demoUserIdentityId"
                                                                                   :roles="definition.roles"
                                                                                   :simulation-is-running="simulation.running"
                                                                                   :disabled="disabled" :is-list="true"
                                                                                   :list-index="index"
                                                                                   @set-data-value="setDataValue"
                                                                                   @clear-item="clearItem"/>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <small
                                                                class="text-muted mr-2 mouse-pointer disable-user-select"
                                                                v-if="index === (data[output.name] || []).length - 1"
                                                                @click="addListItem(output.name)">Hinzufügen</small>
                                                            <small
                                                                class="text-danger opacity-3 mouse-pointer disable-user-select"
                                                                @click="removeItem(output.name, index)">Entfernen</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template
                                                v-if="output.type === 'array' && !(data[output.name] || []).length">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <span style="line-height: 29px;" class="text-muted"><i>Leere Liste</i></span>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted mr-2 mouse-pointer disable-user-select"
                                                               @click="addListItem(output.name)">Hinzufügen</small>
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                        <template v-if="output.type === 'basic'">
                                            <ExecuteActionModalOutputValue :output="output"
                                                                           :output-value="data[output.name] || ''"
                                                                           :demo-user-identity-id="ui.demoUserIdentityId"
                                                                           :roles="definition.roles"
                                                                           :simulation-is-running="simulation.running"
                                                                           :disabled="disabled" :is-list="false"
                                                                           :list-index="null"
                                                                           :processModelPipeNotations="processModelPipeNotations"
                                                                           :environment-values="environmentValues"
                                                                           @set-data-value="setDataValue"
                                                                           @clear-item="clearItem"/>
                                        </template>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-end">
                                    <div class="col-10 p-0">
                                        <div class="pl-1 pr-2  d-flex justify-content-end"
                                             v-if="typeof output.default === 'string'">
                                            <small class="text-muted">
                                                <OptionBadgesWithText :value="output.default"/>
                                            </small>
                                        </div>
                                        <div class="pl-1 pt-1 pr-2"
                                             v-for="error in (ui.validationErrors[output.name] || [])">
                                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 py-2 border-left bg-light" v-if="showSideInfo">
                        <ExecuteActionModalSideInfo :action-type="actionType" :status-types="status_types"
                                                    :definition="definition"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-block py-2 bg-light">
                <div class="row d-flex justify-content-between">
                    <div class="col-7">
                        <div v-if="ui.errorCode" @click="clearError">
                            <span class="text-danger">{{ ui.errorMessage }}</span>
                        </div>
                        <div v-if="!ui.errorCode">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-sm" v-model="demoDataName" type="text"
                                       placeholder="Demo-Datensatz Name..."/>
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-outline-success" @click="addDemoData"
                                            :disabled="savingDemoData">
                                        <span v-if="!savingDemoData"><span class="material-icons">save</span></span>
                                        <img v-else src="/img/loading.gif" alt="Loading" width="13" height="13"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 d-flex justify-content-end">
                        <button v-if="!simulation.starting && !simulation.executing" type="button"
                                class="btn btn-sm btn-light" data-dismiss="modal" @click="$emit('cancel')">
                            Abbrechen
                        </button>
                        <button v-if="(!simulation.starting && !simulation.executing) && !ui.errorCode" type="button"
                                class="ml-2 btn btn-sm btn-success" @click="onSave">{{ title }}
                        </button>
                        <button v-if="(simulation.starting || simulation.executing) && !ui.errorCode"
                                class="ml-2 btn btn-warning btn-sm align-items-center justify-content-between" disabled>
                            <span class="material-icons">more_horiz</span> Laden...
                        </button>
                        <button v-if="ui.errorCode"
                                class="ml-2 btn btn-danger btn-sm align-items-center justify-content-between"
                                @click="clearError">
                            <span class="material-icons mr-2">priority_high</span>
                            <span> {{ ui.errorCode === 422 ? 'Eingabefehler' : 'Error' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalHeader from "./ModalHeader";
import StatusRule from "./StatusRule";
import DefaultItem from "../config/processors/DefaultItem";
import StatesTableSimple from "./StatesTableSimple";
import ConditionsTable from "../config/partials/ConditionsTable";
import Inputs from "./Inputs";
import ExecuteActionModalSideInfo from "./ExecuteActionModalSideInfo";
import ExecuteActionModalOutputValue from "./ExecuteActionModalOutputValue";
import OptionBadgesWithText from "../utils/OptionBadgesWithText.vue";

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        OptionBadgesWithText,
        ExecuteActionModalOutputValue,
        ExecuteActionModalSideInfo,
        Inputs,
        ConditionsTable,
        StatesTableSimple,
        ModalHeader,
        StatusRule,
        DefaultItem
    },
    data() {
        return {
            data: {},
            disabled: [],
            processModelPipeNotations: [],
            demoDataName: '',
            selectedDemoData: '',
            inputs: {},
            savingDemoData: false
        };
    },
    computed: {
        ...mapGetters([
            'action_types',
            'simulation',
            'definition',
            'ui',
            'active_action_type_ids',
            'inaccessible_action_type_ids',
            'active_state_ids',
            'status_types',
            'demo_data',
            'environment_bots',
            'environment_processes',
            'environment_groups',
            'environment_users'
        ]),
        environmentValues() {
            return {
                bots: this.environment_bots,
                processes: this.environment_processes,
                groups: this.environment_groups,
                users: this.environment_users
            }
        },
        title() {
            return this.simulation.running ? 'Ausführen' : 'Starten';
        },
        outputKeys() {
            return this.actionType.outputs.map(ele => ele.name);
        },
        actionType() {
            return this.action_types.find(ele => ele.id === this.ui.modal.data.actionTypeId);
        },
        showSideInfo() {
            return this.actionType.status_rules.length || this.actionType.processors.length || this.actionType.inputs.length;
        },
        actionTypeDemoData() {
            return [...this.demo_data].filter(ele => ele.action_type_id === this.actionType.id);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let data = this.filterDisabledData();

            if (this.simulation.running) {
                // Aktion bei laufender Simulation ausführen
                this.execAction(this.actionType, data)
                    .then(response => this.updateSimulation(response.data))
                    .then(() => this.setViewMode('simulation'))
                    .then(this.closeModal);
            }

            let roleId = this.ui.modal.data.role_id || this.definition.default_role_id || this.definition.public_rule_id || '';
            let environmentId = this.ui.modal.data.environment_id || null;
            let organisationId = this.ui.modal.data.organisation_id || null;
            let userEmail = this.ui.modal.data.user_email || null;

            let options = {
                role_id: roleId,
                environment_id: environmentId,
                organisation_id: organisationId,
                user_email: userEmail
            };

            // Initialaktion beim Starten der Simulation ausführen.
            if (!this.simulation.running) {
                this.startSim(data, options).then(this.closeModal).catch(() => {
                });
            }
        },
        filterDisabledData() {
            let that = this;
            let data = {...this.data};

            that.actionType.outputs.forEach(function (output) {
                if (!data.hasOwnProperty(output.name)) {
                    if (that.outputHasBooleanRule(output)) {
                        data[output.name] = '0';
                    }
                    else {
                        data[output.name] = '';
                    }
                }
                else {
                    data[output.name] = that.data[output.name] || '';
                }
                if (that.disabled.includes(output.name)) {
                    delete data[output.name];
                }
            });

            return data;
        },
        toggleDisable(name) {
            if (this.disabled.includes(name)) {
                this.disabled = this.disabled.filter(ele => ele !== name);
            }
            else {
                this.emptyItem(name);
                this.disabled = [
                    ...this.disabled,
                    name
                ];
            }
        },
        setDataValue(name, value, isArray = false, index = null) {
            if (isArray) {
                let valueArr = this.data[name] || [];

                valueArr[index] = value;
                value = valueArr;
            }

            this.data = {
                ...this.data,
                [name]: value
            };

        },
        emptyItem(name, isArray = false, index = null) {
            this.setDataValue(name, '', isArray, index);
        },
        clearItem(name) {
            let data = {...this.data};
            delete data[name];
            this.data = data;
        },
        resetData() {
            this.selectedDemoData = '';
            this.disabled = [];
            this.data = {...this.inputs};
        },
        loadDemoData(e) {
            if (e.target.value === '') {
                this.resetData();

                return;
            }

            let demoData = this.demo_data.find(ele => ele.action_type_id === this.actionType.id && ele.name === e.target.value) || [];

            if (!demoData.hasOwnProperty('name') || !demoData.hasOwnProperty('values')) {
                return;
            }

            // Jene disablen die nicht in den Demo-Daten drin sind.
            this.disabled = [...this.outputKeys.filter(ele => !Object.keys(demoData.values).includes(ele))];

            // Jene Outputs aus den Demo-Daten filtern, die es nicht mehr gibt.
            this.data = Object.keys(demoData.values).reduce((carry, key) => {
                if (this.outputKeys.includes(key)) {
                    carry[key] = demoData.values[key];
                }

                return carry;
            }, {});
        },
        addDemoData(e) {
            if (!this.demoDataName.trim()) {
                return;
            }

            let that = this;
            let name = this.demoDataName.trim();
            let demoData = [
                ...this.demo_data,
                {
                    action_type_id: this.actionType.id,
                    name: name,
                    values: this.filterDisabledData()
                }
            ];

            this.savingDemoData = true;
            this.saveDemoData(demoData).then(() => {
                that.savingDemoData = false;
                that.selectedDemoData = name;
                that.demoDataName = '';
            });
        },
        deleteDemoData() {
            let that = this;
            let demoData = [...this.demo_data].filter(function (ele) {
                if (ele.action_type_id !== that.actionType.id) {
                    return true;
                }

                return ele.name !== that.selectedDemoData;
            });

            this.saveDemoData(demoData).then(() => that.selectedDemoData = '');
        },
        addListItem(outputName) {
            let arr = this.data[outputName] || [];

            this.data = {
                ...this.data,
                [outputName]: [
                    ...arr,
                    ''
                ]
            };
        },
        removeItem(outputName, arrIndex) {
            let arr = this.data[outputName] || [];
            arr = arr.filter((ele, index) => arrIndex !== index);

            this.data = {
                ...this.data,
                [outputName]: arr
            };
        },
        outputHasBooleanRule(output) {
            return output.validation.find(ele => ele === 'boolean');
        },
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
        // Falls eine Simulation läuft wird hier versucht die Input-Daten zu laden.
        if (this.simulation.running) {
            let that = this;

            this.fetchActionTypeInputs(this.actionType.id, function (response) {
                that.inputs = Object.assign({}, response.data.inputs);
                that.data = Object.assign({}, that.inputs);
            });

            this.fetchList('all').then((response) => {
                let users = [];

                for (let i = 0; i < response.data.data.length; i++) {
                    users.push({
                        label: response.data.data[i].processes_name + ' - ' + response.data.data[i].process_type_metas_name,
                        value: 'process|' + response.data.data[i].processes_id,
                        type: response.data.data[i].process_type_metas_name
                    });
                }

                that.processModelPipeNotations = users;
            });
        }
        else {
            this.data = this.actionType.inputs.reduce((carry, input) => {
                let output = this.actionType.outputs.find(ele => ele.name === input.name);

                if (output === undefined) {
                    return;
                }

                if ('auto' === input.type) {
                    if ('basic' === output.type && !input.value.startsWith('[[') && !input.value.endsWith(']]')) {
                        carry[input.name] = input.value;
                    }
                }
                else if (output.type === input.type) {
                    carry[input.name] = input.value;
                }

                return carry;
            }, {});
        }
    }
};
</script>
