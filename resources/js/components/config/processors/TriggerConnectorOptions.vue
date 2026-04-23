<template>
    <div>
        <template>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="connector">Konnektor</label>
                <select class="form-control" id="connector" name="connector" @change="onChangeConnector"
                        :value="options.connector" :disabled="!ui.editable">
                    <option value="">Bitte wählen...</option>
                    <option v-for="connector in allConnectors"
                            :value="'app::connector|' + connector.identifier + '[Konnektor - ' + connector.name + ']'">
                        {{ connector.name }} - {{ connector.identifier }}
                    </option>
                </select>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="request">Request</label>
                <select class="form-control" id="request" name="request"
                        @change="(e) => $emit('option-change', e.target.name, e.target.value)" :value="options.request"
                        :disabled="!ui.editable">
                    <option value="">Bitte wählen...</option>
                    <option :value="request.identifier" v-for="request in connectorRequests">
                        {{ request.name }} - {{ request.identifier }}
                    </option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label class="mb-0">Rückgabefehler ignorieren?</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="executeAsAuth" :disabled="!ui.editable"
                           :checked="options.allowed_to_fail" @click="toggleAllowedToFail">
                    <label class="custom-control-label" for="executeAsAuth"></label>
                </div>
                <small class="text-muted">Wenn aktiviert, wird die Aktionsausführung bei Status-Code 400-500 (SOAP &
                    HTTP) nicht abgebrochen.</small>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0" for="helper_text">Hilfe-Text</label>
                <textarea name="helper_text" class="form-control" id="helper_text" rows="2" :readonly="!ui.editable"
                          @change="(e) => $emit('option-change', e.target.name, e.target.value)">{{options.helper_text}}</textarea>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0">Anfrage-Mapping</label>
                <div v-if="Object.keys(options.request_mapping).length">
                    <template v-for="(actionOutput, externalActionOutputName) in options.request_mapping">
                        <div class="row mb-2 no-gutters">
                            <div class="col-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                            :disabled="!ui.editable" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <span>{{ externalActionOutputName }}</span>
                                    </button>
                                    <div class="dropdown-menu scrollable-dropdown">
                                        <button type="button" class="dropdown-item"
                                                v-for="newExernalOutputKey in usableRequestMappingBindingNames"
                                                @click="onChangeKeyMapping('request_mapping', newExernalOutputKey, externalActionOutputName)">
                                            <span>{{ newExernalOutputKey }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <textarea class="form-control" rows="2"
                                          @change="onChangeRequestMapping(externalActionOutputName, $event.target.value)"
                                          :readonly="!ui.editable"
                                          v-bind:value="options.request_mapping[externalActionOutputName]"></textarea>
                                <OptionBadgesWithText :value="options.request_mapping[externalActionOutputName]"
                                                      display-block hide-on-empty/>
                            </div>
                            <div class="col-1 px-2">
                                <DropdownSelector :action-type="actionType" :dropdown-width="640"
                                                  :outputs-from-actiontype-only="true"
                                                  :syntax-include="Object.keys(syntaxLoaderLabels())" v-if="ui.editable"
                                                  @selected="onAppendValueMapping($event, externalActionOutputName)"/>
                            </div>
                            <div class="col-1">
                                <button class="btn btn-sm btn-outline-danger float-right"
                                        @click="onDeleteRequestMapping(externalActionOutputName)" v-if="ui.editable">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <span v-else class="d-block">-</span>
                <span class="d-block text-muted mb-2" v-if="Object.keys(options.request_mapping).length">Anfrage-Binding-Name ← Wert. </span>
                <div class="d-flex justify-content-start" v-if="usableRequestMappingBindingNames.length && ui.editable">
                    <button class="btn btn-sm btn-outline-success" @click="onAddRequestMapping">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
            <div class="form-group input-group-sm mb-2">
                <label class="mb-0">Antwort-Mapping</label>
                <div v-if="Object.keys(options.response_mapping).length">
                    <template v-for="(responseMappingValue, responseBindingName) in options.response_mapping">
                        <div class="input-group input-group-sm mb-2">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span>{{ responseBindingName }}</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button type="button" class="dropdown-item"
                                            v-for="newBindingKey in usableResponseMappingBindingNames"
                                            @click="onChangeKeyMapping('response_mapping', newBindingKey, responseBindingName)">
                                        <span>{{ newBindingKey }}</span>
                                    </button>
                                </div>
                            </div>
                            <input class="form-control" :value="responseMappingValue" @input="onChangeValueMapping"
                                   :data-name="'response_mapping'" :data-output="responseBindingName">
                            <div class="input-group-append">
                                <button class="btn btn-outline-danger"
                                        @click="onDeleteResponseMapping(responseBindingName)">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <span v-else class="d-block">-</span>
                <small class="d-block text-muted mb-0" v-if="Object.keys(options.response_mapping).length">Aktion-Output
                    ← Anfrage-Antwort Wert</small>
                <small class="d-block text-muted mb-2" v-if="Object.keys(options.response_mapping).length">Greife mit
                    der "."-Notation auf einen Wert einer JSON-Antwort zu (z.B. "[[connector_response.body.feld_1]]".
                    Wähle "[[connector_response.body]]" für die
                    gesamte Rückgabe. Mit "[[connector_response.headers.Content-Type]]" kann z.B. der Content-Type
                    Header gewählt werden.</small>
                <div class="d-flex justify-content-start"
                     v-if="usableResponseMappingBindingNames.length && actionType.outputs.length">
                    <button class="btn btn-sm btn-outline-success" @click="onAddResponseMapping">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>

<script>

import {mapGetters} from "vuex";
import utils from "../../../config-utils";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText.vue";
import DropdownSelector from "../../utils/DropdownSelector.vue";

export default {
    components: {
        DropdownSelector,
        OptionBadgesWithText,
        AutocompleteSelector
    },
    props: {
        options: Object,
        actionType: Object
    },
    computed: {
        ...mapGetters([
            'environments',
            'ui'
        ]),
        requestMappingString() {
            let requestMapping = this.options.request_mapping instanceof Array ? {} : this.options.request_mapping || null;
            return !requestMapping ? '' : JSON.stringify(requestMapping, null, 4);
        },
        responseMappingString() {
            let responseMapping = this.options.request_mapping instanceof Array ? {} : this.options.request_mapping || null;
            return !responseMapping ? '' : JSON.stringify(responseMapping, null, 4);
        },
        usableRequestMappingBindingNames() {
            return this.requestBindingNames.filter(ele => !Object.keys(this.options.request_mapping).includes(ele)).sort((a, b) => a.localeCompare(b));
        },
        usableResponseMappingBindingNames() {
            return this.actionType.outputs.map(ele => ele.name).filter(ele => !Object.keys(this.options.response_mapping).includes(ele));
        },
        allConnectors() {
            let connectors = [];

            for (let i = 0; i < this.environments.length; i++) {
                let environment = this.environments[i];

                for (let k = 0; k < environment.blueprint.connectors.length; k++) {
                    let connector = environment.blueprint.connectors[k];
                    let exists = connectors.find(ele => ele.id === connector.id);

                    if (!exists) {
                        connectors.push(connector);
                    }
                }
            }

            return connectors;
        },
        connectorRequests() {
            if (!this.options.connector) {
                return [];
            }

            let parts = this.getSyntaxParts(this.options.connector);
            let connector = this.allConnectors.find(ele => ele.identifier === parts.key);

            if (!connector) {
                return [];
            }

            let requests = [];

            for (let i = 0; i < this.environments.length; i++) {
                let environment = this.environments[i];

                for (let k = 0; k < environment.blueprint.requests.length; k++) {
                    let request = environment.blueprint.requests[k];
                    let exists = requests.find(ele => ele.id === request.id);

                    if (!exists && connector.id === request.connector_id) {
                        requests.push(request);
                    }
                }
            }

            return requests;
        },
        requestBindingNames() {
            let bindingNames = [];
            let request = this.options.request;

            if (this.options.connector && request) {
                let parts = this.getSyntaxParts(this.options.connector);
                let connector = this.allConnectors.find(ele => ele.identifier === parts.key);

                for (let i = 0; i < this.environments.length; i++) {
                    let requests = this.environments[i].blueprint.requests;

                    for (let k = 0; k < requests.length; k++) {
                        if (request === requests[k].identifier && connector.id === requests[k].connector_id) {
                            bindingNames = [
                                ...bindingNames,
                                ...requests[k].bindings.map(ele => ele.name)
                            ];
                        }
                    }
                }
            }

            return bindingNames;
        }
    },
    methods: {
        ...utils,
        updateRequestMapping(newVal) {
            this.$emit('option-change', 'request_mapping', newVal);
        },
        updateResponseMapping(newVal) {
            this.$emit('option-change', 'response_mapping', newVal);
        },
        onChangeKeyMapping(optionName, newExternalOutputKey, oldExternalOutputKey) {
            let mapping = {...this.options[optionName]};
            let value = mapping[oldExternalOutputKey];
            delete mapping[oldExternalOutputKey];

            this.$emit('option-change', optionName, {
                ...mapping,
                [newExternalOutputKey]: value
            });
        },
        onChangeRequestMapping(key, value) {
            let mapping = {...this.options.request_mapping};

            this.$emit('option-change', 'request_mapping', {
                ...mapping,
                [key]: value
            });
        },
        onAppendValueMapping(item, key) {
            let value = this.options.request_mapping.hasOwnProperty(key) ? this.options.request_mapping[key] + item.value_with_label : item.value_with_label;

            this.onChangeRequestMapping(key, value);
        },
        onChangeValueMapping(e) {
            let mappingKey = e.target.dataset.output;
            let optionName = e.target.dataset.name;
            let mapping = {...this.options[optionName] || {}};

            this.$emit('option-change', optionName, {
                ...mapping,
                [mappingKey]: e.target.value
            });
        },
        onAddRequestMapping() {
            if (!this.usableRequestMappingBindingNames.length) {
                return;
            }

            this.$emit('option-change', 'request_mapping', {
                ...this.options.request_mapping,
                [this.usableRequestMappingBindingNames[0]]: ''
            });
        },
        onAddResponseMapping() {
            if (!this.usableResponseMappingBindingNames.length) {
                return;
            }

            this.$emit('option-change', 'response_mapping', {
                ...this.options.response_mapping,
                [this.usableResponseMappingBindingNames[0]]: ''
            });
        },
        onDeleteRequestMapping(mappingKey) {
            let mapping = {...this.options.request_mapping};
            delete mapping[mappingKey];
            this.$emit('option-change', 'request_mapping', mapping);
        },
        onDeleteResponseMapping(mappingKey) {
            let mapping = {...this.options.response_mapping};
            delete mapping[mappingKey];
            this.$emit('option-change', 'response_mapping', mapping);
        },
        setConnector(name) {
            this.$emit('option-change', 'connector', 'app::connector|' + name);
        },
        setRequest(name) {
            this.$emit('option-change', 'request', 'app::request|' + name);
        },
        onChangeConnector(e) {
            this.$emit('option-change', 'connector', e.target.value);
            this.$emit('option-change', 'request', '');
        },
        toggleAllowedToFail(e) {
            this.$emit('option-change', 'allowed_to_fail', e.target.checked);
        }
    }
};
</script>
