<template>
    <div>
        <div class="modal-header py-2">
            <h5 class="modal-title" id="exampleModalLabel">
                <button class="btn btn-sm btn-primary mr-2" @click="$emit('navigation-change', 'Variables')">
                    <span class="material-icons">keyboard_backspace</span>
                </button>
                <span>{{ addMode ? 'Variable hinzufügen' : 'Variable bearbeiten' }}</span>
            </h5>
            <button type="button" class="close" aria-label="Close" @click="$emit('cancel')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body py-2">
            <div class="form-group input-group-sm mb-3">
                <label class="mb-0" for="identifier">Identifier</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" maxlength="40" required
                           v-model="variable.identifier" :readonly="!ui.editable">
                    <div class="input-group-append" v-if="ui.editable">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                data-toggle="dropdown" aria-expanded="false">Demo
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button"
                                    @click="variable.identifier = 'demo_variable_1'">demo_variable_1
                            </button>
                            <button class="dropdown-item" type="button"
                                    @click="variable.identifier = 'demo_variable_2'">demo_variable_2
                            </button>
                            <button class="dropdown-item" type="button"
                                    @click="variable.identifier = 'demo_variable_3'">demo_variable_3
                            </button>
                        </div>
                    </div>
                </div>
                <div v-for="error in (ui.validationErrors.identifier || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-2">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="typeString" name="typeString" value="TYPE_STRING"
                           class="custom-control-input" v-model="variable.type" @change="onTypeChange">
                    <label class="custom-control-label" for="typeString">Zeichenkette</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="typeJson" name="typeJson" value="TYPE_JSON" class="custom-control-input"
                           v-model="variable.type" @change="onTypeChange">
                    <label class="custom-control-label" for="typeJson">JSON</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="typeDocument" name="typeDocument" value="TYPE_DOCUMENT"
                           class="custom-control-input" v-model="variable.type" @change="onTypeChange">
                    <label class="custom-control-label" for="typeDocument">Dokument</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="typeSmartVariable" name="typeSmartVariable" value="TYPE_SMART_VARIABLE"
                           class="custom-control-input" v-model="variable.type" @change="onTypeChange">
                    <label class="custom-control-label" for="typeSmartVariable">Smarte Variable</label>
                </div>
                <div v-for="error in (ui.validationErrors.type || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group">
                <div v-if="variable.type === 'TYPE_STRING'" class="mt-2">
                    <div class="form-group input-group-sm mb-2">
                        <textarea class="form-control" rows="1" @input="variable.value = $event.target.value"
                                  v-bind:value="variable.value" :readonly="!ui.editable"></textarea>
                    </div>
                    <div v-for="error in (ui.validationErrors.value || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div v-if="variable.type === 'TYPE_JSON'">
                    <small class="text-muted">
                        <span>JSON-Eingabe - </span>
                        <span class="material-icons">info</span>
                        <span>Alle Zeichenketten-Werte werden getrimmt und leere Werte werden zu NULL gecastet.</span>
                    </small>
                    <CodeEditor :code="jsonValue" @update-code="onCodeChange" :max-length="1500" :watch-code-prop="true"
                                :editable="ui.editable"/>
                    <div>
                        <small v-if="variable.value.length > 1499" class="text-danger">Wert zu lang!</small>
                        <small v-else-if="invalidCode" class="text-danger">Ungültiges JSON</small>
                        <small v-else class="text-success">Gültiges JSON</small>
                    </div>
                </div>
                <div v-if="variable.type === 'TYPE_DOCUMENT'">
                    <span
                        class="text-muted">Für eine Demo wird automatisch ein Dokument für diese Variable erzeugt.</span>
                </div>
                <div v-if="variable.type === 'TYPE_SMART_VARIABLE'" class="mt-2">
                    <div class="form-group input-group-sm mb-2">
                        <textarea class="form-control" rows="1" @input="variable.value = $event.target.value"
                                  v-bind:value="variable.value" :readonly="!ui.editable"></textarea>
                    </div>
                    <div v-for="error in (ui.validationErrors.value || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
            </div>
        </div>
        <ModalFooter @save="onSave" @cancel="onCancel" :ui="ui" :cancel-label="'Zurück'"
                     :save-label="addMode ? 'Hinzufügen' : 'Übernehmen'"/>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {v4 as uuidv4} from 'uuid';
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import CodeEditor from "../CodeEditor";

export default {
    components: {
        CodeEditor,
        ModalHeader,
        ModalFooter
    },
    props: {
        environment: Object,
        processVersionId: String,
        data: {
            required: true,
            default: null
        }
    },
    data() {
        let data = this.data;
        let addMode = this.data === null;
        let id = uuidv4();

        if (!this.data) {
            data = {
                id: id,
                identifier: '',
                type: 'TYPE_STRING',
                value: '',
                is_public: false
            };
        }

        return {
            variable: {...data},
            file: '',
            invalidCode: false,
            addMode: addMode
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        jsonValue() {
            if (!this.variable.value) {
                return {};
            }

            try {
                return typeof this.variable.value === 'string' ? JSON.parse(this.variable.value) : this.variable.value;
            } catch (e) {
                return {};
            }
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreVariable' : 'UpdateVariable';
            this.patchBlueprint(method, this.variable).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
                this.$emit('navigation-change', 'Variables');
            });
        },
        onCancel() {
            this.clearError();
            this.$emit('navigation-change', 'Variables');
        },
        navigationChange(to, variable) {
            this.$emit('navigation-change', to, variable);
        },
        onTypeChange() {
            this.file = '';
            this.invalidCode = false;
            this.variable.is_public = false;

            if (this.variable.type === 'TYPE_JSON') {
                this.variable.value = {};
            }
            else if (this.variable.type === 'TYPE_DOCUMENT') {
                this.variable.value = '[[faker.file.pdf.raw]]';
                this.variable.is_public = true;
            }
            else if (this.variable.type === 'TYPE_SMART_VARIABLE') {
                this.variable.value = 0;
            }
            else {
                this.variable.value = '';
            }
        },
        onCodeChange(code) {
            this.invalidCode = false;
            let obj = false;

            try {
                obj = JSON.parse(code.trim());
            } catch (e) {
                this.invalidCode = true;
                return;
            }

            this.variable.value = obj;
        },
    },
    watch: {
        variable: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }
};
</script>
