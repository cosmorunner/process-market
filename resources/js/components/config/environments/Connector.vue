<template>
    <div>
        <div class="modal-header py-2">
            <h5 class="modal-title" id="exampleModalLabel">
                <button class="btn btn-sm btn-primary mr-2" @click="$emit('navigation-change', 'Connectors')">
                    <span class="material-icons">keyboard_backspace</span>
                </button>
                <span>{{ addMode ? 'Konnektor hinzufügen' : 'Konnektor bearbeiten' }}</span>
            </h5>
            <button type="button" class="close" aria-label="Close" @click="$emit('cancel')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body py-2">
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required v-model="connector.name" maxlength="40" :readonly="!ui.editable" />
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="identifier">Identifier</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" maxlength="40" required v-model="connector.identifier" :readonly="!ui.editable">
                    <div class="input-group-append" v-if="ui.editable">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">Demo</button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button" @click="connector.identifier = 'demo_connector_1'">demo_connector_1</button>
                            <button class="dropdown-item" type="button" @click="connector.identifier = 'demo_connector_2'">demo_connector_2</button>
                            <button class="dropdown-item" type="button" @click="connector.identifier = 'demo_connector_3'">demo_connector_3</button>
                        </div>
                    </div>
                </div>
                <div v-for="error in (ui.validationErrors.identifier || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="type">Typ</label>
                <select class="form-control" id="type" name="type" v-model="connector.type" :disabled="!ui.editable">
                    <option value="http">HTTP</option>
                    <option value="soap">SOAP</option>
                    <option value="sftp">SFTP</option>
                    <option value="database">Datenbank</option>
                </select>
                <div v-for="error in (ui.validationErrors.type || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <template v-if="!addMode">
                <span class="d-block">Anfragen</span>
                <ul v-if="requests.length" v-for="request in requests"
                    class="list-group list-group-flush">
                    <li class="list-group-item p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-10 hover-pointer p-2" @click="navigationChange('Request', request)">
                                    <span>{{ request.name }} - {{ request.identifier }}</span>
                                </div>
                                <div class="col-2 p-2">
                                    <button class="btn btn-sm btn-light float-right" @click="onDeleteRequest(request.id)" v-if="ui.editable">
                                        <span class="material-icons text-danger">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <button class="btn btn-sm my-2 btn-outline-primary" @click="navigationChange('Request', newRequest)" v-if="ui.editable">
                    <span class="material-icons">add</span>
                </button>
            </template>
        </div>
        <ModalFooter @save="onSave"
                     @cancel="onCancel"
                     :ui="ui"
                     :save-label="addMode ? 'Hinzufügen' : 'Speichern'"
        />
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {v4 as uuidv4} from 'uuid';
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import AddSituation from "./AddSituation";
import AddProcessData from "./AddProcessData";
import AddAccess from "./AddAccess";

export default {
    components: {
        ModalHeader,
        ModalFooter,
        AddSituation,
        AddProcessData,
        AddAccess
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
                name: '',
                identifier: '',
                type: 'http'
            };
        }

        return {
            connector: {...data},
            addMode: addMode,
            newRequest: {
                id: uuidv4(),
                connector_id: data.id || id,
                name: null,
                identifier: '',
                bindings: [],
                debug_options: {
                    type: 'json',
                    body: {
                        example: "data"
                    }
                }
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        requests() {
            return this.environment.blueprint.requests.filter(ele => ele.connector_id === this.connector.id);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreConnector' : 'UpdateConnector';
            this.patchBlueprint(method, this.connector).then((response) => {
                this.$emit('navigation-change', 'Connectors');
                this.$emit('blueprint-change', response.data.blueprint);
            });
        },
        onCancel() {
            this.clearError();
            this.$emit('navigation-change', 'Connectors');
        },
        navigationChange(to, connector) {
            this.$emit('navigation-change', to, connector);
        },
        onDeleteRequest(id) {
            this.patchBlueprint('DeleteRequest', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            });
        }
    },
    watch: {
        connector: {
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
