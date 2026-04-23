<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-4" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <ul v-for="publicApi in blueprint.public_apis" class="list-group list-group-flush">
                <li class="list-group-item p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-10 p-2">
                                <span>{{ types[publicApi.type] }}:</span>
                                <span>{{ publicApi.name }}</span>
                                <span> - </span>
                                <span class="text-muted">{{ publicApi.slug }}</span>
                            </div>
                            <div class="col-2 p-2">
                                <button class="btn btn-sm btn-light float-right"
                                        @click="onDeletePublicApi(publicApi.id)" v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="row mt-2" v-if="ui.editable">
                <div class="col-4">Name</div>
                <div class="col-4">Slug</div>
                <div class="col-4">Typ</div>
                <div class="col-4"></div>
            </div>
            <div class="row" v-if="ui.editable">
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" v-model="newPublicApi.name"/>
                    <div class="invalid-feedback d-block mt-0" v-for="error in [...ui.validationErrors.name || []]">
                        {{ error }}
                    </div>
                </div>
                <div class="col-4">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" maxlength="40" required v-model="newPublicApi.slug">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-expanded="false">Demo
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" type="button" @click="newPublicApi.slug = 'demo-public-api-1'">demo-public-api-1</button>
                                <button class="dropdown-item" type="button" @click="newPublicApi.slug = 'demo-public-api-2'">demo-public-api-2</button>
                                <button class="dropdown-item" type="button" @click="newPublicApi.slug = 'demo-public-api-3'">demo-public-api-3</button>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block mt-0" v-for="error in [...ui.validationErrors.slug || []]">
                        {{ error }}
                    </div>
                </div>
                <div class="col-3">
                    <select class="form-control form-control-sm" v-model="newPublicApi.type">
                        <option :value="value" v-for="(label, value) in types">{{ label }}</option>
                    </select>
                    <div class="invalid-feedback d-block mt-0" v-for="error in [...ui.validationErrors.type || []]">
                        {{ error }}
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn btn-sm btn-outline-primary" type="button" @click="onAddPublicApi">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
        <ModalFooter :ui="ui" :save-label="'Speichern'" v-on="$listeners" />
    </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import {v4 as uuidv4} from "uuid";
import Navigation from "./Navigation";
import ModalHeader from "../ModalHeader";
import ModalFooter from "../ModalFooter";
import utils from "../../../config-utils";
import {reduxActions} from "../../../store/develop-and-config";

export default {
    components: {
        Navigation,
        ModalHeader,
        ModalFooter,
    },
    props: {
        detailComponentName: String,
        environment: Object,
        addMode: Boolean,
        processVersionId: String
    },
    data() {
        return {
            newPublicApi: {
                name: '',
                slug: '',
                type: 'list'
            },
            types: {
                list: 'Liste',
                action: 'Aktion',
                initial_action: 'Initialaktion',
                process: 'Prozess'
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        },
        onAddPublicApi() {
            if (this.newPublicApi.name === '' || this.newPublicApi.slug === '') {
                return;
            }

            this.patchBlueprint('StorePublicApi', this.newPublicApi).then((response) => {
                this.resetnewPublicApi();
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        onDeletePublicApi(id) {
            this.patchBlueprint('DeletePublicApi', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        resetnewPublicApi() {
            this.newPublicApi = {
                id: uuidv4(),
                name: '',
                slug: ''
            };
        }
    },
    watch: {
        newPublicApi: {
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
