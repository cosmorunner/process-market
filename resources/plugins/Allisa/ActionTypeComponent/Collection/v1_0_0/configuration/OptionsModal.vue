<template>
    <div>
        <div class="container">
            <div class="row d-flex">
                <div class="col">
                    <div class="form-group input-group-sm">
                        <label class="mb-0">Liste</label>
                        <select class="form-control" v-model="options.list_config_id" :disabled="!editable"
                                @change="onChangeListConfig">
                            <option :value="listConfig.id" v-for="listConfig in data.listConfigs">
                                {{ listConfig.name }} - {{ listConfig.slug }}
                            </option>
                        </select>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['list_config_id'] || [])">{{ error }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="mb-0">Daten-Mapping</label>
                        <template v-for="(actionOutput, listDataAlias) in (options.mapping || {})">
                            <div class="input-group input-group-sm mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="listDataAlias">{{ listDataAlias }}</span>
                                </div>
                                <input type="text" readonly class="form-control" aria-label="Label"
                                       :value="actionOutput"/>
                                <div class="input-group-append" v-if="editable">
                                    <button class="btn btn-outline-danger" @click="onDeleteMapping(listDataAlias)">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="d-flex justify-content-start" v-if="editable">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle rounded-0 "
                                            type="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <span>{{ newMappingKey }}</span>
                                    </button>
                                    <div class="dropdown-menu scrollable-dropdown">
                                        <button type="button" class="dropdown-item"
                                                v-for="dataField in listConfigColumnAliases"
                                                @click="newMappingKey = dataField">
                                            <span>{{ dataField }}</span>
                                        </button>
                                    </div>
                                </div>
                                <input type="text" class="form-control form-control-sm rounded-0 rounded-left"
                                       v-model="newMappingValue"/>
                                <div class="input-group-append">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle rounded-0 mr-1"
                                                type="button" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <span>#</span>
                                        </button>
                                        <div class="dropdown-menu scrollable-dropdown">
                                            <button type="button" class="dropdown-item"
                                                    v-for="output in data.actionTypeOutputs"
                                                    @click="newMappingValue = output.name">
                                                <span>{{ output.name }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success" @click="onAddMapping">
                                        <span class="material-icons">add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="d-block text-muted"
                               v-if="data.actionTypeOutputs.length || Object.keys(options.binding || {}).length">
                            <span>Listen-Datensatz Alias → Formular-Feld</span>
                        </small>
                        <small class="text-muted"
                               v-if="data.actionTypeOutputs.length || Object.keys(options.binding || {}).length">Nur
                            a-z, 0-9 und "_".</small>
                    </div>
                    <div class="mb-3">
                        <label class="mb-0">URL Query-Parameter Binding</label>
                        <template v-for="(actionOutput, queryParam) in (options.binding || {})">
                            <div class="input-group input-group-sm mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">{{ queryParam }}</span>
                                </div>
                                <input type="text" readonly class="form-control" aria-label="Label"
                                       :value="actionOutput"/>
                                <div class="input-group-append" v-if="editable">
                                    <button class="btn btn-outline-danger" @click="onDeleteBinding(queryParam)">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="d-flex justify-content-start" v-if="editable">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <input type="text" placeholder="z.B. feld_1" aria-describedby="Query Parameter"
                                           class="form-control form-control-sm rounded-0 rounded-left"
                                           style="border-top-left-radius: 0.25rem !important; border-bottom-left-radius: 0.25rem !important;"
                                           v-model="newBindingKey" aria-label="Query Parameter">
                                </div>
                                <input type="text" class="form-control form-control-sm rounded-0 rounded-left"
                                       v-model="newBindingValue"/>
                                <div class="input-group-append">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle rounded-0 mr-1"
                                                type="button" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <span>#</span>
                                        </button>
                                        <div class="dropdown-menu scrollable-dropdown">
                                            <button type="button" class="dropdown-item"
                                                    v-for="output in data.actionTypeOutputs"
                                                    @click="newBindingValue = output.name">
                                                <span>{{ output.name }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success" @click="onAddBinding">
                                        <span class="material-icons">add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="d-block text-muted"
                               v-if="data.actionTypeOutputs.length || Object.keys(options.binding || {}).length">
                            <span>URL Query-Parameter ← Formular-Feld</span>
                        </small>
                        <small class="text-muted"
                               v-if="data.actionTypeOutputs.length || Object.keys(options.binding || {}).length">Nur
                            a-z, 0-9 und "_".</small>
                    </div>
                    <div class="mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1"
                                   @click="toggleApplyContextParameter" :checked="options.apply_context_parameter"
                                   :disabled="!editable">
                            <label class="custom-control-label" for="customSwitch1">"context"-URL-Parameter
                                übernehmen</label>
                            <small class="text-muted d-block">Den aktuellen "context"-URL-Parameter aus der URL
                                übernehmen.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1"
                                   @click="toggleHideWhenEmpty" :checked="options.hide_when_empty"
                                   :disabled="!editable">
                            <label class="custom-control-label" for="customSwitch1">Verstecken wenn die Liste leer
                                ist.</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    props: {
        data: Object,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        validationErrors: Object | Array,
        clearError: Function,
        editable: Boolean
    },
    data() {
        return {
            options: {...this.data.options},
            listConfigColumnAliases: [],
            newMappingKey: '#',
            newMappingValue: '',
            newBindingKey: '',
            newBindingValue: ''
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        selectedListConfig() {
            return this.data.listConfigs.find(ele => ele.id === this.options.list_config_id);
        }
    },
    methods: {
        onChangeListConfig() {
            this.loadSelectItems();
        },
        onAddMapping() {
            if (!this.newMappingKey.trim() || !this.newMappingValue.trim() || !/^[a-z0-9_]+$/.test(this.newMappingKey)) {
                return;
            }

            this.options = {
                ...this.options,
                mapping: {
                    ...this.options.mapping,
                    [this.newMappingKey.trim()]: this.newMappingValue.trim()
                }
            };

            this.newMappingKey = '#';
            this.newMappingValue = '';
        },
        onDeleteMapping(mappingKey) {
            let mapping = {...this.options.mapping};
            delete mapping[mappingKey];

            this.options = {
                ...this.options,
                mapping: mapping
            };
        },
        onAddBinding() {
            if (!this.newBindingKey.trim() || !this.newBindingValue.trim() || !/^[a-z0-9_]+$/.test(this.newBindingKey)) {
                return;
            }

            this.options = {
                ...this.options,
                binding: {
                    ...this.options.binding,
                    [this.newBindingKey.trim()]: this.newBindingValue.trim()
                }
            };

            this.newBindingKey = '';
            this.newBindingValue = '';
        },
        onDeleteBinding(key) {
            let binding = {...this.options.binding};
            delete binding[key];

            this.options = {
                ...this.options,
                binding: binding
            };
        },
        toggleHideWhenEmpty() {
            this.options = {
                ...this.options,
                hide_when_empty: !this.options.hide_when_empty
            };
        },
        toggleApplyContextParameter() {
            this.options = {
                ...this.options,
                apply_context_parameter: !this.options.apply_context_parameter
            };
        },
        loadSelectItems() {
            let that = this;

            if (this.selectedListConfig) {
                axios.get(this.ui.urls.list_support.replace('-listConfigId-', this.selectedListConfig.id) + '?parts=select').then(function (response) {
                    let aliases = response.data.select.map(ele => ele.alias || '').filter(ele => ele !== '');

                    that.listConfigColumnAliases = aliases.filter(ele => !Object.keys(that.options.mapping || {}).includes(ele));
                }).catch(function () {
                    that.loading = false;
                    console.log(error);
                });
            }
        }
    },
    watch: {
        $data: {
            handler: function (data) {
                // Mit "update-parent-data-" werden die Daten an das Eltern-Modal übergeben, wo die Daten abgelegt werden.
                // Wenn dann der Benutzer "Speichern" klickt (onConfirm), werden diese abgelegten Daten an die
                // onConfirm-Methode übergeben.
                this.$emit('update-parent-data', data.options);
            },
            deep: true
        }
    },
    mounted() {
        // Nachdem das Modal initialisiert wurde, müssen wir im Eltern-Modal die Daten ablegen,
        // die gespeichert werden, wenn der Benutzer auf "Speichern" klickt. Diese Daten werden
        // der onConfirm-Methode übergeben.
        this.$emit('update-parent-data', this.data.options);
        this.loadSelectItems();
    }
};
</script>

