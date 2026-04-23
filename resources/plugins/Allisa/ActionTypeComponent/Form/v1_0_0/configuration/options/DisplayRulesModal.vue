<template>
    <div>
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
                    <DisplayRulesTable :group-labels="groupLabels" :part="'shown'" :operator-labels="operatorLabels"
                                       :display-rules="displayRules.shown || []" :editable="editable"
                                       @delete-item="onDeleteItem"

                    />
                    <DisplayRulesAdd v-if="editable" :input-names="inputNames" :part="'shown'"
                                     :group-labels="groupLabels" :operator-labels="operatorLabels"
                                     :display-rules="displayRules.shown  || []" :roles="roles"
                                     @display-rules-change="onDisplayRulesChange"/>
                </div>
            </div>
            <!-- hidden -->
            <div v-if="ruleParts.includes('hidden')" class="row d-flex">
                <div class="col mt-2">
                    <span class="d-block mb-2">
                        <span class="material-icons">visibility_off</span>
                        <span>Verstecken wenn...</span>
                        <button class="btn btn-sm px-1 p-0 btn-light" @click="always('hidden')" v-if="!alwaysHidden && editable">
                            <small>Immer</small>
                        </button>
                    </span>
                    <div v-if="alwaysHidden" class="mb-3">
                        <span>Immer</span>
                        <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                @click="clearAlways('hidden')" v-if="editable">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <DisplayRulesTable v-if="!alwaysHidden" :group-labels="groupLabels" :part="'hidden'"
                                       :operator-labels="operatorLabels" :display-rules="displayRules.hidden"
                                       :editable="editable" @delete-item="onDeleteItem"

                    />
                    <DisplayRulesAdd v-if="!alwaysHidden && editable" :input-names="inputNames" :part="'hidden'"
                                     :group-labels="groupLabels" :operator-labels="operatorLabels" :roles="roles"
                                     :display-rules="displayRules.hidden" @display-rules-change="onDisplayRulesChange"/>
                    <small class="text-muted">
                        <span class="material-icons">priority_high</span>
                        <span>Werte von versteckten Feldern werden nicht an den Server gesendet.</span>
                    </small>
                </div>
            </div>
            <!-- required -->
            <div v-if="ruleParts.includes('required')" class="row d-flex">
                <div class="col mt-2">
                    <span class="d-block mb-2">
                        <span class="material-icons">priority_high</span>
                        <span>Pflichtfeld wenn...</span>
                        <button class="btn btn-sm px-1 p-0 btn-light" @click="always('required')" v-if="!alwaysRequired && editable">
                            <small>Immer</small>
                        </button>
                    </span>
                    <div v-if="alwaysRequired" class="mb-3">
                        <span>Immer</span>
                        <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                @click="clearAlways('required')" v-if="editable">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <DisplayRulesTable v-if="!alwaysRequired" :group-labels="groupLabels" :part="'required'"
                                       :operator-labels="operatorLabels" :display-rules="displayRules.required"
                                       :editable="editable" @delete-item="onDeleteItem"

                    />
                    <DisplayRulesAdd v-if="!alwaysRequired && editable" :input-names="inputNames" :part="'required'"
                                     :group-labels="groupLabels" :operator-labels="operatorLabels"
                                     :display-rules="displayRules.required" :roles="roles"
                                     @display-rules-change="onDisplayRulesChange"/>
                </div>
            </div>
            <!-- readonly -->
            <div v-if="ruleParts.includes('readonly')" class="row d-flex">
                <div class="col mt-2">
                    <span class="d-block mb-2">
                        <span class="material-icons">border_color</span>
                        <span>"Nur lesen" wenn...</span>
                        <button class="btn btn-sm px-1 p-0 btn-light" @click="always('readonly')" v-if="!alwaysReadonly && editable">
                            <small>Immer</small>
                        </button>
                    </span>
                    <div v-if="alwaysReadonly" class="mb-3">
                        <span>Immer</span>
                        <button class="btn btn-sm p-1 btn-light text-danger font-weight-normal"
                                @click="clearAlways('readonly')" v-if="editable">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <DisplayRulesTable v-if="!alwaysReadonly" :group-labels="groupLabels" :part="'readonly'"
                                       :operator-labels="operatorLabels" :display-rules="displayRules.readonly"
                                       :editable="editable" @delete-item="onDeleteItem"

                    />
                    <DisplayRulesAdd v-if="!alwaysReadonly && editable" :input-names="inputNames" :part="'readonly'"
                                     :group-labels="groupLabels" :operator-labels="operatorLabels"
                                     :display-rules="displayRules.readonly" :roles="roles"
                                     @display-rules-change="onDisplayRulesChange"/>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <small class="text-muted">
                        <span class="material-icons">help</span>
                        <span>Markierte Checkboxen haben den Wert "1".</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import DisplayRulesTable from "./partials/DisplayRulesTable";
import DisplayRulesAdd from "./partials/DisplayRulesAdd";

export default {
    components: {
        DisplayRulesAdd,
        DisplayRulesTable,
        ModalHeader,
        ModalFooter,
    },
    props: {
        data: {
            required: true,
            type: Object
        },
        loading: {
            required: true,
            type: Boolean
        },
        errorCode: {
            required: true,
            type: Number | null
        },
        errorMessage: {
            required: true,
            type: String | null
        },
        clearError: {
            required: true,
            type: Function
        },
        editable: {
            default: true,
            type: Boolean
        }
    },
    data() {
        return {
            displayRules: {
                ...this.data.displayRules || {},
                shown: [...this.data.displayRules.shown || []],
                hidden: [...this.data.displayRules.hidden || []],
                required: [...this.data.displayRules.required || []],
                readonly: [...this.data.displayRules.readonly || []]
            },
            ruleParts: this.data.ruleParts || [
                'shown',
                'hidden',
                'required',
                'readonly'
            ],
            inputNames: [...this.data.inputNames],
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
            roles: this.data.roles || []
        };
    },
    computed: {
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
        }
    },
    methods: {
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
    watch: {
        field: {
            handler: function () {
                if (this.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        $data: {
            handler: function (data) {
                // Mit "update-parent-data-" werden die Daten an das Eltern-Modal übergeben, wo die Daten abgelegt werden.
                // Wenn dann der Benutzer "Speichern" klickt (onConfirm), werden diese abgelegten Daten an die
                // onConfirm-Methode übergeben.
                this.$emit('update-parent-data', data.displayRules);
            },
            deep: true
        }
    },
    mounted() {
        // Nachdem das Modal initialisiert wurde, müssen wir im Eltern-Modal die Daten ablegen,
        // die gespeichert werden, wenn der Benutzer auf "Speichern" klickt. Diese Daten werden
        // der onConfirm-Methode übergeben.
        this.$emit('update-parent-data', this.data.displayRules);
    }
};
</script>

