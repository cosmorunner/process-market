<template>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <ModalHeader
                :title="ui.modal.data.modalTitle ? 'Anzeige-Regeln für ' + ui.modal.data.modalTitle : 'Anzeige Regeln'"/>
            <div class="modal-body py-2">
                <div class="container">
                    <!-- show -->
                    <div v-if="ruleParts.includes('shown')" class="row d-flex">
                        <div class="col">
                            <span class="d-block mb-2">
                                <span class="material-icons">visibility</span>
                                <span>Anzeigen wenn...</span>
                            </span>
                            <div v-if="alwaysShown" class="mb-3">
                                <span>Immer</span>
                            </div>
                            <DisplayRulesTable :group-labels="groupLabels" :part="'shown'"
                                               :operator-labels="operatorLabels"
                                               :display-rules="displayRules.shown || []" :editable="ui.editable"
                                               @delete-item="onDeleteItem"

                            />
                            <DisplayRulesAdd v-if="ui.editable" :input-names="inputNames" :part="'shown'"
                                             :roles="roles" :group-labels="groupLabels" :operator-labels="operatorLabels"
                                             :display-rules="displayRules.shown  || []"
                                             @display-rules-change="onDisplayRulesChange"/>
                        </div>
                    </div>
                    <!-- hidden -->
                    <div v-if="ruleParts.includes('hidden')" class="row d-flex">
                        <div class="col">
                            <span class="d-block mb-2">
                                <span class="material-icons">visibility_off</span>
                                <span>Verstecken wenn...</span>
                                <button class="btn btn-sm px-1 p-0 btn-light" @click="always('hidden')"
                                        v-if="!alwaysHidden && ui.editable">
                                    <small>Immer</small>
                                </button>
                            </span>
                            <div v-if="alwaysHidden" class="mb-3">
                                <span>Immer</span>
                                <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                        @click="clearAlways('hidden')" v-if="ui.editable">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <DisplayRulesTable v-if="!alwaysHidden" :group-labels="groupLabels" :part="'hidden'"
                                               :operator-labels="operatorLabels"
                                               :display-rules="displayRules.hidden || []" :editable="ui.editable"
                                               @delete-item="onDeleteItem"

                            />
                            <DisplayRulesAdd v-if="!alwaysHidden && ui.editable" :input-names="inputNames"
                                             :roles="roles"
                                             :part="'hidden'" :group-labels="groupLabels"
                                             :operator-labels="operatorLabels" :display-rules="displayRules.hidden"
                                             @display-rules-change="onDisplayRulesChange"/>
                            <small class="text-muted">
                                <span class="material-icons">priority_high</span>
                                <span>Werte von versteckten Feldern werden nicht an den Server gesendet.</span>
                            </small>
                        </div>
                    </div>
                    <!-- required -->
                    <div v-if="ruleParts.includes('required')" class="row d-flex">
                        <div class="col">
                            <span class="d-block mb-2">
                                <span class="material-icons">priority_high</span>
                                <span>Pflichtfeld wenn...</span>
                                <button class="btn btn-sm px-1 p-0 btn-light" @click="always('required')"
                                        v-if="!alwaysRequired && ui.editable">
                                    <small>Immer</small>
                                </button>
                            </span>
                            <div v-if="alwaysRequired" class="mb-3">
                                <span>Immer</span>
                                <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                        @click="clearAlways('required')" v-if="ui.editable">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <DisplayRulesTable v-if="!alwaysRequired" :group-labels="groupLabels" :part="'required'"
                                               :operator-labels="operatorLabels"
                                               :display-rules="displayRules.required || []" :editable="ui.editable"
                                               @delete-item="onDeleteItem"

                            />
                            <DisplayRulesAdd v-if="!alwaysRequired && ui.editable" :input-names="inputNames"
                                             :roles="roles" :part="'required'" :group-labels="groupLabels"
                                             :operator-labels="operatorLabels" :display-rules="displayRules.required || []"
                                             @display-rules-change="onDisplayRulesChange"/>
                        </div>
                    </div>
                    <!-- readonly -->
                    <div v-if="ruleParts.includes('readonly')" class="row d-flex">
                        <div class="col">
                            <span class="d-block mb-2">
                                <span class="material-icons">border_color</span>
                                <span>"Nur lesen" wenn...</span>
                                <button class="btn btn-sm px-1 p-0 btn-light" @click="always('readonly')"
                                        v-if="!alwaysReadonly && ui.editable">
                                    <small>Immer</small>
                                </button>
                            </span>
                            <div v-if="alwaysReadonly" class="mb-3">
                                <span>Immer</span>
                                <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                        @click="clearAlways('readonly')" v-if="ui.editable">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <DisplayRulesTable v-if="!alwaysReadonly" :group-labels="groupLabels" :part="'readonly'"
                                               :operator-labels="operatorLabels" :display-rules="displayRules.readonly"
                                               :editable="ui.editable" @delete-item="onDeleteItem"

                            />
                            <DisplayRulesAdd v-if="!alwaysReadonly && ui.editable" :input-names="inputNames"
                                             :roles="roles" :part="'readonly'" :group-labels="groupLabels"
                                             :operator-labels="operatorLabels" :display-rules="displayRules.readonly"
                                             @display-rules-change="onDisplayRulesChange"/>
                        </div>
                    </div>
                    <!-- disabled -->
                    <div v-if="ruleParts.includes('disabled')" class="row d-flex">
                        <div class="col">
                            <span class="d-block mb-2">
                                <span class="material-icons">block</span>
                                <span>Deaktiviert wenn...</span>
                                <button class="btn btn-sm px-1 p-0 btn-light" @click="always('disabled')"
                                        v-if="!alwaysDisabled && ui.editable">
                                    <small>Immer</small>
                                </button>
                            </span>
                            <div v-if="alwaysDisabled" class="mb-3">
                                <span>Immer</span>
                                <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                        @click="clearAlways('disabled')">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                            <DisplayRulesTable :group-labels="groupLabels" :part="'disabled'"
                                               :operator-labels="operatorLabels" :display-rules="displayRules.disabled"
                                               :editable="ui.editable" @delete-item="onDeleteItem"

                            />
                            <DisplayRulesAdd v-if="!alwaysDisabled && ui.editable" :input-names="inputNames"
                                             :roles="roles" :part="'disabled'" :group-labels="groupLabels"
                                             :operator-labels="operatorLabels" :display-rules="displayRules.disabled"
                                             @display-rules-change="onDisplayRulesChange"/>
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'"/>
        </div>
    </div>
</template>

<script>

import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import DisplayRulesTable from "./DisplayRulesTable";
import DisplayRulesAdd from "./DisplayRulesAdd";
import {mapActions, mapGetters} from "vuex";
import utils from "../../config-utils";
import {reduxActions} from "../../store/develop-and-config";

export default {
    components: {
        DisplayRulesAdd,
        DisplayRulesTable,
        ModalHeader,
        ModalFooter,
    },
    data() {
        return {
            displayRules: {
                shown: [],
                hidden: [],
                required: [],
                readonly: [],
                disabled: []
            },
            ruleParts: [
                'shown',
                'hidden',
                'required',
                'readonly',
                'disabled'
            ],
            inputNames: [],
            roles: [],
            operatorLabels: {
                '=': 'Gleich',
                '!=': 'Nicht gleich',
                '<': 'Kleiner als',
                '<=': 'Kleiner oder gleich',
                '>=': 'Größer oder gleich',
                '>': 'Größer als',
                'contains': 'Beinhaltet',
                'not_contains': 'Beinhaltet nicht'
            },
            groupLabels: {
                'group_1': 'Gruppe 1',
                'group_2': 'Gruppe 2',
                'group_3': 'Gruppe 3',
                'group_4': 'Gruppe 4',
                'group_5': 'Gruppe 5',
                'group_6': 'Gruppe 6',
                'group_7': 'Gruppe 7',
                'group_8': 'Gruppe 8',
                'group_9': 'Gruppe 9',
                'group_10': 'Gruppe 10',
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        alwaysShown() {
            return !this.displayRules.shown.length;
        },
        alwaysHidden() {
            return this.displayRules.hidden.length === 1 && (this.displayRules.hidden[0][0] || '') === 'always';
        },
        alwaysRequired() {
            return this.displayRules.required.length === 1 && (this.displayRules.required[0][0] || '') === 'always';
        },
        alwaysReadonly() {
            return this.displayRules.readonly.length === 1 && (this.displayRules.readonly[0][0] || '') === 'always';
        },
        alwaysDisabled() {
            return this.displayRules.disabled.length === 1 && (this.displayRules.disabled[0][0] || '') === 'always';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let payload = {
                ...this.ui.modal.data.component,
                options: {
                    ...this.ui.modal.data.component.options,
                    display: this.displayRules
                }
            };

            this.patchDefinition('UpdateComponent', payload).then(this.closeModal).catch(this.closeModal);
        },
        onDisplayRulesChange(type, rules) {
            this.displayRules = {
                ...this.displayRules,
                [type]: rules
            };
        },
        onDeleteItem(part, item) {
            let displayRules = {...this.displayRules};
            let partRules = displayRules[part].filter(function (ele) {
                return JSON.stringify(ele) !== JSON.stringify(item);
            });

            this.displayRules = {
                ...this.displayRules,
                [part]: partRules
            };
        },
        always(part) {
            this.displayRules = {
                ...this.displayRules,
                [part]: [
                    [
                        'always',
                        '1',
                        '=',
                        '1'
                    ]
                ]
            };
        },
        clearAlways(part) {
            this.displayRules = {
                ...this.displayRules,
                [part]: []
            };
        }
    },
    mounted() {
        if (this.ui.modal.data.component) {
            let component = this.ui.modal.data.component;
            let displayRules = component.options.display || {};

            this.displayRules = {
                ...displayRules,
                shown: [...(displayRules.shown || [])],
                hidden: [...(displayRules.hidden || [])],
                required: [...(displayRules.required || [])],
                readonly: [...(displayRules.readonly || [])],
                disabled: [...(displayRules.disabled || [])]
            };
            this.ruleParts = this.ui.modal.data.ruleParts || [];
            this.inputNames = this.ui.modal.data.inputNames;
            this.roles = this.ui.modal.data.roles;
        }
    }
};
</script>

