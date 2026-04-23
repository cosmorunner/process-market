<template>
    <div>
        <div class="modal-header py-2">
            <h5 class="modal-title" id="exampleModalLabel">
                <button class="btn btn-sm btn-primary mr-2" @click="$emit('navigation-change', 'Connector', connector)">
                    <span class="material-icons">keyboard_backspace</span>
                </button>
                <span>{{ addMode ? 'Request hinzufügen' : 'Request bearbeiten' }}</span>
            </h5>
            <button type="button" class="close" aria-label="Close" @click="$emit('cancel')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body py-2">
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required v-model="request.name" maxlength="40"
                       :readonly="!ui.editable"/>
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="identifier">Identifier</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" maxlength="40" required v-model="request.identifier"
                           :readonly="!ui.editable">
                    <div class="input-group-append" v-if="ui.editable">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false" :disabled="!ui.editable">Demo
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" @click="request.identifier = 'demo_request_1'">demo_request_1
                            </button>
                            <button class="dropdown-item" type="button" @click="request.identifier = 'demo_request_2'">demo_request_2
                            </button>
                            <button class="dropdown-item" type="button" @click="request.identifier = 'demo_request_3'">demo_request_3
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <template v-if="Object.keys(request.bindings).length || ui.editable">
                <span class="d-block">Bindings</span>
                <table class="table table-sm table-borderless m-0 mb-1">
                    <tbody>
                    <tr v-for="binding in request.bindings" class="d-flex">
                        <td class="py-0 text-muted w-20 mb-1">{{ binding.name }}</td>
                        <td class="py-0 text-muted w-75 mb-1">{{ binding.description }}</td>
                        <td class="py-0 mb-1">
                            <button class="btn btn-sm btn-light float-right" @click="onDeleteBinding(binding.name)" v-if="ui.editable">
                                <span class="material-icons text-danger">close</span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </template>
            <div class="input-group input-group-sm mb-2" v-if="ui.editable">
                <input type="text" class="form-control w-20" placeholder="Name..." v-model="newBinding.name"
                       maxlength="40"/>
                <input type="text" class="form-control w-75" placeholder="Beschreibung..."
                       v-model="newBinding.description" maxlength="200"/>
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" @click="onAddBinding">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
            <div class="form-group input-group-sm">
                <label class="mb-0" for="name">Debug Optionen</label>
                <CodeEditor :code="debugOptions" @update-code="updateDebugOptions" :editable="ui.editable"/>
                <small class="text-muted">
                    <span class="material-icons">{{ validJson ? 'done' : 'close' }}</span>
                    <span>JSON-Objekt</span>
                </small>
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
        </div>
        <ModalFooter @save="onSave"
                     @cancel="onCancel"
                     :ui="ui"
                     :save-disabled="!validJson"
                     :save-label="addMode ? 'Hinzufügen' : 'Übernehmen'"
        />
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import CodeEditor from "../CodeEditor";

export default {
    components: {
        ModalHeader,
        ModalFooter,
        CodeEditor
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
        let addMode = this.data.name === null;

        return {
            request: {...data},
            addMode: addMode,
            validJson: true,
            newBinding: {
                name: '',
                description: ''
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        connector() {
            return this.environment.blueprint.connectors.find(ele => ele.id === this.request.connector_id);
        },
        debugOptions() {
            let debugOptions = this.request.debug_options instanceof Array ? {} : this.request.debug_options || null;

            return !debugOptions ? '' : JSON.stringify(debugOptions, null, 4);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreRequest' : 'UpdateRequest';

            this.patchBlueprint(method, this.request).then((response) => {
                this.$emit('navigation-change', 'Connectors');
                this.$emit('blueprint-change', response.data.blueprint);
            });
        },
        onCancel() {
            this.clearError();
            this.$emit('navigation-change', 'Connector', this.connector);
        },
        navigationChange(to, request) {
            this.$emit('navigation-change', to, request);
        },
        onAddBinding() {
            if (this.request.bindings.map(ele => ele.name).includes(this.newBinding.name) || this.newBinding.name === '') {
                return;
            }

            this.request = {
                ...this.request,
                bindings: [
                    ...this.request.bindings,
                    this.newBinding
                ]
            };
            this.newBinding = {
                name: '',
                description: ''
            };
        },
        onDeleteBinding(name) {
            this.request = {
                ...this.request,
                bindings: [...this.request.bindings].filter(ele => ele.name !== name)
            };
        },
        updateDebugOptions(newVal) {
            this.validJson = true;
            let obj = false;

            try {
                obj = JSON.parse(newVal);
            } catch (e) {
                this.validJson = false;

                return;
            }

            if (obj && typeof obj === 'object') {
                this.validJson = true;
                this.request.debug_options = JSON.parse(newVal);
                return;
            }

            this.validJson = false;
        }
    },
    watch: {
        request: {
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
