<template>
    <div class="modal-dialog modal-lg" role="document" id="data-modal">
        <div class="modal-content">
            <ModalHeader :title="createMode ? 'Vorlade-Datenfeld erstellen' : 'Vorlade-Datenfeld bearbeiten'"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form>
                            <div class="form-group mb-2">
                                <label for="name" class="mb-0">Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" :readonly="!ui.editable"
                                           id="name" v-model="data.name" aria-describedby="name" required>
                                    <div class="input-group-append" v-if="createMode">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            #
                                            <span class="sr-only">Toggle Syntax</span>
                                        </button>
                                        <div class="dropdown-menu scrollable-dropdown">
                                            <button class="dropdown-item" type="button" v-for="name in uniqueNames"
                                                    @click="data.name = name">{{ name }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Kleingeschrieben, nur "a-z", "0-9" und "_".</small>
                                <div v-for="error in (ui.validationErrors.name || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="mb-0">Typ</label>
                                <div class="my-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeValue"
                                               id="typeAuto" value="auto" v-model="inputType" :disabled="!ui.editable">
                                        <label class="form-check-label" for="typeAuto">Automatisch</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeValue"
                                               id="typeBasic" value="basic" v-model="inputType" :disabled="!ui.editable">
                                        <label class="form-check-label" for="typeBasic">Zeichenkette</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeValue"
                                               id="typeArray" value="array" v-model="inputType" :disabled="!ui.editable">
                                        <label class="form-check-label" for="typeArray">JSON-Array</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeValue"
                                               id="typeObject" value="object" v-model="inputType" :disabled="!ui.editable">
                                        <label class="form-check-label" for="typeObject">JSON-Objekt</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="typeValue"
                                               id="typeListConfig" value="list_config" v-model="inputType" :disabled="!ui.editable">
                                        <label class="form-check-label" for="typeListConfig">Listen-Inhalt</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="mb-0">Wert</label>
                                <div v-if="inputType === 'basic'" class="mt-2">
                                    <div class="form-group input-group-sm mb-2">
                                        <textarea class="form-control" rows="2"
                                                  @input="data.value = $event.target.value" :readonly="!ui.editable"
                                                  v-bind:value="data.value"></textarea>
                                        <OptionBadgesWithText :value="data.value" display-block hide-on-empty />
                                    </div>
                                    <div class="form-group input-group-sm mb-2" v-if="ui.editable">
                                        <DropdownSelector
                                            :syntax-include="Object.keys(syntaxLoaderLabels()).filter(ele => ele !== 'action.outputs')"
                                            @selected="onSelectDropdown"
                                            :dropdown-width="766"
                                        />
                                        <button class="btn btn-link btn-sm" type="button" @click="setProcessDataValue" v-if="data.name.trim()">
                                            Prozessdatenwert für '{{data.name.trim()}}' setzen.
                                        </button>
                                    </div>
                                    <small class="text-muted d-block">Daten vom Typ "Liste" oder "Objekt" werden
                                        JSON-kodiert</small>
                                    <small class="text-muted d-block" v-if="data.value === '[[url.query.NAME]]'">Ersetzen
                                        Sie "NAME" mit den Namen des URL Query-Parameters</small>
                                    <div v-for="error in (ui.validationErrors.value || [])">
                                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                    </div>
                                </div>
                                <div v-if="inputType === 'auto'" class="mt-2">
                                    <div class="form-group input-group-sm mb-2">
                                        <textarea class="form-control" rows="2"
                                                  @input="data.value = $event.target.value" :readonly="!ui.editable"
                                                  v-bind:value="data.value"></textarea>
                                        <OptionBadgesWithText :value="data.value" display-block hide-on-empty />
                                    </div>
                                    <div class="form-group input-group-sm mb-2" v-if="ui.editable">
                                        <DropdownSelector
                                            :syntax-include="Object.keys(syntaxLoaderLabels()).filter(ele => ele !== 'action.outputs')"
                                            @selected="onSelectDropdown"
                                            :dropdown-width="766"
                                        />
                                        <button class="btn btn-link btn-sm" type="button" @click="setProcessDataValue" v-if="data.name.trim()">
                                            Prozessdatenwert für '{{data.name.trim()}}' setzen.
                                        </button>
                                    </div>
                                    <small class="text-muted d-block" v-if="data.value === '[[url.query.NAME]]'">Ersetzen
                                        Sie "NAME" mit den Namen des URL Query-Parameters</small>
                                    <small class="text-muted d-block">Daten vom Typ "Automatisch" werden je nach Wert automatisch
                                        zu einer Zeichenkette, JSON-Objekt oder JSON-Array gecastet.</small>
                                    <div v-for="error in (ui.validationErrors.value || [])">
                                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                    </div>
                                </div>
                                <div v-if="inputType === 'array' || inputType === 'object'">
                                    <small class="text-muted">
                                        <span>JSON-Eingabe - </span>
                                        <span class="material-icons">info</span>
                                        <span>Alle Zeichenketten-Werte werden getrimmt.</span>
                                    </small>
                                    <CodeEditor :code="JSON.stringify(data.value, null, 4)" :editable="ui.editable"
                                                @update-code="onCodeChange" :max-length="1500" :watch-code-prop="true"/>
                                    <div>
                                        <small v-if="data.value.length > 1499" class="text-danger">Wert zu lang!</small>
                                        <small v-else-if="invalidCode" class="text-danger">Ungültiges JSON</small>
                                        <small v-else class="text-success">Gültiges JSON</small>
                                    </div>
                                    <DropdownSelector v-if="ui.editable"
                                        :syntax-include="Object.keys(syntaxLoaderLabels()).filter(ele => ele !== 'action.outputs')"
                                        @selected="copyText"
                                        :dropdown-width="766"
                                    />
                                    <span class="text-success" v-if="showCopied">Kopiert!</span>
                                </div>
                                <div v-if="inputType === 'list_config'">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr class="d-flex">
                                            <th class="col-6 border-bottom-0 border-top-0"><small>Liste</small></th>
                                            <th class="col-6 border-bottom-0 border-top-0"><small>Spalte</small></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="d-flex">
                                            <td class="col-6">
                                                <select class="form-control form-control-sm" id="listConfig"
                                                        @change="onChangeListConfig" :disabled="!ui.editable"
                                                        :value="selectedListConfig ? selectedListConfig.id : null">
                                                    <option :value="null">Bitte wählen...</option>
                                                    <option v-for="listConfig in list_configs" :value="listConfig.id">
                                                        {{ listConfig.name }} - {{ listConfig.slug }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="col-6">
                                                <select class="form-control form-control-sm" id="column" :value="data.type_options.column"
                                                        v-if="selectedListConfig" @change="onChangeColumn" :disabled="!ui.editable">
                                                    <option :value="''">Komplette Liste</option>
                                                    <option v-for="dataAlias in (selectedListConfig.data_aliases || [])"
                                                            :value="dataAlias">{{ dataAlias }}
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" v-on="$listeners" :on-save="onSave" :save-label="title"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "../config/CodeEditor";
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import DropdownSelector from "../utils/DropdownSelector";
import OptionBadgesWithText from "../utils/OptionBadgesWithText.vue";

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector,
        CodeEditor,
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            showCopied: false,
            invalidCode: false,
            data: {
                action_type_id: this.actionTypeId,
                name: '',
                value: '',
                type: 'auto',
                type_options: {}
            },
            old_name: '',
            createMode: true,
            updateMode: false
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'outputs',
            'action_types',
            'list_configs',
        ]),
        actionTypeId() {
            return this.ui.modal.data.actionTypeId;
        },
        actionType() {
            return this.action_types.find(ele => ele.id === this.actionTypeId);
        },
        title() {
            return this.createMode ? 'Erstellen' : 'Speichern';
        },
        uniqueNames() {
            let actionTypeOutputNames = this.action_types.reduce(function (carry, ele) {
                return [
                    ...carry,
                    ...ele.outputs.map(ele => ele.name)
                ];
            }, []);

            let outputNames = this.outputs.map(ele => ele.name);

            return [
                ...new Set([
                    ...actionTypeOutputNames,
                    ...outputNames
                ])
            ].sort((a, b) => a > b);
        },
        inputType: {
            get() {
                return this.data.type;
            },
            set(val) {
                this.data.type = val;

                switch (val) {
                    case 'object':
                        this.data.value = {};
                        this.data.type_options = {};
                        break;

                    case 'array':
                        this.data.value = [];
                        this.data.type_options = {};
                        break;

                    case 'list_config':
                        this.data.value = [];
                        this.data.type_options = {
                            list_config: null,
                            column: null
                        };
                        break;

                    case 'auto':
                        this.data.value = '';
                        this.data.type_options = {};
                        break;
                    default:
                        this.data.value = '';
                        this.data.type_options = {};
                }
            }
        },
        selectedListConfig() {
            if (!this.data.type_options.list_config) {
                return null;
            }

            let parts = this.getSyntaxParts(this.data.type_options.list_config);

            return this.list_configs.find(ele => ele.id === parts.key) || null;
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let data = {
                ...this.data,
                name: this.data.name.toLowerCase(),
                action_type_id: this.actionTypeId,
                value: this.data.value,
                old_name: this.old_name
            };

            this.patchDefinition(this.ui.modal.data.method, data, false).then(this.closeModal).catch(() => {
            });
        },
        onCodeChange(code) {
            this.code = code.trim();
            this.invalidCode = false;
            let obj = false;

            try {
                obj = JSON.parse(this.code);
            } catch (e) {
                this.invalidCode = true;
                return;
            }

            this.data.value = obj;
        },
        onSelectDropdown(item) {
            this.data = {
                ...this.data,
                value: this.data.value + item.value_with_label
            };
        },
        copyText(item) {
            let that = this;

            this.copy(item.value_with_label).then(function () {
                that.showCopied = true;
            }).catch(() => console.log('error copying'));

            setTimeout(() => {
                this.showCopied = false;
            }, 1500);
        },
        copy(textToCopy) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(textToCopy);
            }
            else {
                let textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.getElementById('data-modal').appendChild(textArea);
                textArea.focus();
                textArea.select();

                return new Promise((res, rej) => {
                    document.execCommand("copy") ? res() : rej();
                    textArea.remove();
                });
            }
        },
        onChangeListConfig(e) {
            let listConfig = this.list_configs.find(ele => ele.id === e.target.value);

            if (listConfig) {
                let label = 'Liste - ' + listConfig.name + ' - ' + listConfig.slug;

                this.data.type_options = {
                    ...this.data.type_options,
                    column: null,
                    list_config: this.setSyntaxLabel('listConfig|' + listConfig.id, label)
                };
            }
        },
        onChangeColumn(e) {
            this.data.type_options = {
                ...this.data.type_options,
                column: e.target.value
            };
        },
        setProcessDataValue(){
            this.data.value = "[[process.outputs." + this.data.name.trim() + "((Prozess-Daten - " + this.data.name.trim() + "))]]";
        }
    },
    watch: {
        data: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    mounted() {
        if (this.ui.modal.data.input) {
            this.createMode = false;
            this.updateMode = true;
            this.data = {
                ...this.data,
                ...this.ui.modal.data.input
            };
            this.old_name = this.data.name;
        }
    }
};
</script>
