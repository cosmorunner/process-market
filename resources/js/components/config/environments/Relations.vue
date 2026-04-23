<template>
    <div>
        <ModalHeader :title="addMode ? 'Neue Umgebung' : 'Umgebung bearbeiten'" v-on="$listeners"/>
        <div class="modal-body py-2">
            <Navigation class="mb-2" :detail-component-name="detailComponentName" v-on="$listeners"/>
            <div>
                <ul v-for="relation in blueprint.relations" class="list-group list-group-flush">
                    <li class="list-group-item p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-10 p-2">
                                    <div>
                                        <span>{{ getProcess(relation.left).name }}</span>
                                        <span class="material-icons mx-1">arrow_forward_ios</span>
                                        <OptionBadges :value="relation.relation_type"/>
                                        <span class="material-icons mx-1">arrow_back_ios</span>
                                        <span>{{ getProcess(relation.right).name }}</span>
                                    </div>
                                    <ul class="list-group" v-if="Object.keys(relation.data).length">
                                        <li class="px-0 py-1 text-muted border-0 list-group-item"
                                            v-for="(value, key) in relation.data">{{ key }}:
                                            <OptionBadges :value="value"/>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-2 p-2">
                                    <button class="btn btn-sm btn-light float-right"
                                            @click="onDeleteRelation(relation.id)" v-if="ui.editable">
                                        <span class="material-icons text-danger">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <small v-if="!blueprint.processes.length" class="text-muted">Legen Sie zunächst Prozesse an.</small>
                <div class="row mt-2" v-if="ui.editable">
                    <div class="col-3">Linker Prozess</div>
                    <div class="col-6">Verknüpfungstyp</div>
                    <div class="col-3">Rechter Prozess</div>
                </div>
                <div class="row" v-if="ui.editable">
                    <div class="col-3">
                        <select class="form-control form-control-sm" v-model="newRelation.left">
                            <option value="">Bitte wählen...</option>
                            <option :value="simulation.default_allisa_process_id">Demo-Prozess</option>
                            <option :value="simulation.default_allisa_user_identity_id">Demo-Benutzer -
                                Prozess-Identität
                            </option>
                            <option v-for="process in blueprint.processes" :value="process.id">{{
                                    process.name
                                }}
                            </option>
                        </select>
                    </div>
                    <div class="col-6">
                        <AutocompleteSelector :items="newRelation.relation_type ? [newRelation.relation_type] : []"
                                              :label="''" :icon="'settings_ethernet'" :max-items="1"
                                              :pipe-include="['graphs_relation_types', 'relation_types']"
                                              @items-changed="onChangeRelationType"/>
                    </div>
                    <div class="col-3">
                        <select class="form-control form-control-sm" v-model="newRelation.right">
                            <option value="">Bitte wählen...</option>
                            <option v-for="process in blueprint.processes" :value="process.id">{{
                                    process.name
                                }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="my-3">
                    <div
                        v-for="error in ([...(ui.validationErrors.left || []), ...(ui.validationErrors.relation_type || []), ...(ui.validationErrors.right || [])] || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="mb-3"
                     v-if="Object.keys(newRelation.data).length || (selectedRelationType && Object.keys(selectedRelationType.default).length) && ui.editable">
                    <label class="mb-1 mt-3">Verknüpfungsdaten</label>
                    <div class="row mt-2">
                        <div class="col-3">Name</div>
                        <div class="col-6">Wert</div>
                        <div class="col-3"></div>
                    </div>
                    <template v-for="(dataValue, dataKey) in newRelation.data || {}">
                        <div class="row mt-2">
                            <div class="col-3">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span>{{ dataKey }}</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button type="button" class="dropdown-item"
                                            v-for="newExernalOutputKey in usableRelationTypeDataKeys"
                                            @click="onChangeKey(newExernalOutputKey, dataKey)">
                                        <span>{{ newExernalOutputKey }}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-6">
                                <AutocompleteSelector :items="dataValue && dataValue.length ? [dataValue] : []"
                                                      :max-items="1" :add-only-from-autocomplete="false"
                                                      :syntax-include="Object.keys(syntaxLoaderLabels())"
                                                      @items-changed="onChangeDataValue(dataKey, $event)"/>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-sm btn-outline-danger" @click="onDeleteKey(dataKey)">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div class="d-flex justify-content-start mt-2" v-if="usableRelationTypeDataKeys.length">
                        <button class="btn btn-sm btn-outline-success" @click="onAddData">
                            <span class="material-icons">add</span>
                        </button>
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-end" v-if="ui.editable">
                    <button class="btn btn-sm btn-outline-primary float-right" type="button" @click="onAddRelation">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
        <ModalFooter :ui="ui" :save-label="'Speichern'" v-on="$listeners"/>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapActions, mapGetters} from "vuex";
import {v4 as uuidv4} from "uuid";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import Navigation from "./Navigation";
import {reduxActions} from "../../../store/develop-and-config";
import SortedRelationTypes from "../../utils/SortedRelationTypes";
import OptionBadges from "../../utils/OptionBadges";
import AutocompleteSelector from "../../utils/AutocompleteSelector";

export default {
    components: {
        AutocompleteSelector,
        OptionBadges,
        SortedRelationTypes,
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
            newRelation: {
                id: uuidv4(),
                left: '',
                right: '',
                relation_type: '',
                relation_type_name: '',
                data: {}
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'graphs',
            'process',
            'simulation',
            'definition',
            'graphs_relation_types',
            'relation_types'
        ]),
        blueprint() {
            return this.environment.blueprint;
        },
        usableRelationTypeDataKeys() {
            if (!this.selectedRelationType) {
                return [];
            }

            return Object.keys(this.selectedRelationType.default || {}).filter(ele => !Object.keys(this.newRelation.data).includes(ele));
        },
        selectedRelationType() {
            if (!this.newRelation.relation_type) {
                return null;
            }

            let parts = this.getSyntaxParts(this.newRelation.relation_type);

            if(parts.namespace) {
                return this.graphs_relation_types.find(ele => ele.reference === parts.key) || null;
            } else {
                return this.relation_types.find(ele => ele.reference === parts.key);
            }
        },
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        navigationChange(to, process) {
            this.$emit('navigation-change', to, process);
        },
        resetNewRelation() {
            this.newRelation = {
                id: uuidv4(),
                left: '',
                right: '',
                relation_type: '',
                relation_type_name: '',
                namespace: '',
                data: {}
            };
        },
        onAddRelation() {
            this.patchBlueprint('StoreRelation', this.newRelation).then((response) => {
                this.resetNewRelation();
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        onDeleteRelation(id) {
            this.patchBlueprint('DeleteRelation', {id}).then((response) => {
                this.$emit('blueprint-change', response.data.blueprint);
            }).catch(() => {
            });
        },
        relationTypes(processId = null) {
            if (processId === this.simulation.default_allisa_process_id) {
                return this.definition.relation_types;
            }

            let process = this.blueprint.processes.find(ele => ele.id === processId);

            if (!process) {
                return [];
            }

            let graph = this.graphs.find(ele => ele.full_namespace === process.process_type);

            if (!graph) {
                return [];
            }

            return graph.relation_types;
        },
        getProcess(id) {
            if (id === this.simulation.default_allisa_process_id) {
                return {
                    name: 'Demo-Prozess'
                };
            }

            if (id === this.simulation.default_allisa_user_identity_id) {
                return {
                    name: 'Demo-Benutzer - Prozess-Identität'
                };
            }

            return this.blueprint.processes.find(ele => ele.id === id) || {
                name: 'unknown'
            };
        },
        getRelationTypeLabel(relation) {
            if (relation.relation_type.split('::')[0] === this.process.full_namespace) {
                return relation.relation_type_name;
            }

            return relation.relation_type.split('::')[0] + ' - ' + relation.relation_type_name;
        },
        onChangeRelationType(autocompleteItems) {
            let parts = this.getSyntaxParts(autocompleteItems[0]);
            let relationType = null;
            let relationTypePipeNotation = null;

            // If the syntax parts have a namespace, an external relation type was selected.
            if (parts.namespace) {
                relationType = this.graphs_relation_types.find(ele => ele.namespace === parts.namespace && ele.reference === parts.key);
                relationTypePipeNotation = autocompleteItems[0];
            }
            else {
                relationType = this.relation_types.find(ele => ele.reference === parts.key);
                relationTypePipeNotation = this.process.full_namespace + '::' + autocompleteItems[0];
            }

            if (relationType) {
                this.newRelation.relation_type = relationTypePipeNotation;
                this.newRelation.relation_type_name = relationType.name;
            }
            else {
                this.newRelation.relation_type = '';
                this.newRelation.relation_type_name = '';
                this.newRelation.data = {};
            }
        },
        onDeleteKey(key) {
            let data = {...this.newRelation.data};
            delete data[key];
            this.newRelation.data = data;
        },
        onChangeKey(newExternalOutputKey, mappingKey) {
            let data = {...this.newRelation.data};
            let value = data[mappingKey];

            delete data[mappingKey];

            this.newRelation.data = {
                ...data,
                [newExternalOutputKey]: value
            };
        },
        onInputValue(e) {
            let key = e.target.dataset.key;
            let data = {...this.newRelation.data};

            this.newRelation.data = {
                ...data,
                [key]: e.target.value
            };
        },
        onChangeValue(key, value) {
            let data = {...this.newRelation.data};

            this.newRelation.data = {
                ...data,
                [key]: value
            };
        },
        onChangeDataValue(key, value) {
            this.onChangeValue(key, value[0] || '');
        },
        onAddData() {
            if (!this.usableRelationTypeDataKeys.length) {
                return;
            }

            let nextKey = this.usableRelationTypeDataKeys[0];
            let defaultVal = this.selectedRelationType.default[nextKey] || '';

            this.newRelation.data = {
                ...this.newRelation.data,
                [this.usableRelationTypeDataKeys[0]]: defaultVal
            };
        },
    },
    watch: {
        newRelation: {
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
