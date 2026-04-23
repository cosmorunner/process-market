<template>
    <div>
        <div class="form-group input-group-sm mb-0" v-if="template.hasOwnProperty('data')">
            <table class="table table-sm m-0">
                <tbody>
                <tr class="d-flex">
                    <td class="col-3 font-weight-normal border-0">
                        <div class="input-group input-group-sm mb-0">
                            <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <span class="material-icons">search</span>
                                    </span>
                            </div>
                            <input type="text" class="form-control" v-model="search" placeholder="Suche..."
                                   aria-label="Suche" aria-describedby="basic-addon1" autofocus>
                            <div class="input-group-append" v-if="search">
                                <button class="btn btn-sm btn-outline-danger" type="button" id="button-addon2"
                                        @click="search = ''">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="col-3 font-weight-normal border-0">
                        <span class="text-muted">Beschreibung</span>
                    </td>
                    <td class="col-2 font-weight-normal border-0">
                        <span class="text-muted">Typ</span>
                    </td>
                    <td class="col-3 font-weight-normal border-0">
                        <span class="text-muted">Wert</span>
                    </td>
                    <td class="col-1 font-weight-normal border-0"></td>
                </tr>
                </tbody>
            </table>
            <table class="table table-sm mb-0 d-block"
                   :style="'overflow-y:scroll;max-height:' + (ui.modal.bodyHeight - 190) + 'px'">
                <tbody class="d-block">
                <tr class="d-flex" v-if="!allMappingKeysSorted.length && search">
                    <td class="col-12">Kein Eintrag.</td>
                </tr>
                <tr v-for="name in allMappingKeysSorted"
                    :class="{'d-flex': true, 'bg-light': !allMapping[name].global}">
                    <template v-if="editMappingKey !== name">
                        <td class="col-3">
                            <span :class="{'text-secondary': !allMapping[name].global}">{{ name }}</span>
                            <span class="badge badge-pill badge-light" v-if="allMapping[name].global">Global</span>
                        </td>
                        <td class="col-3">
                            <span>{{ allMapping[name].description }}</span>
                        </td>
                        <td class="col-2">
                            <span v-if="allMapping[name].type === 'string'">Zeichenkette</span>
                            <span v-if="allMapping[name].type === 'array'">JSON Objekt/Array</span>
                            <span v-if="allMapping[name].type === 'ListConfig'">Listeninhalt</span>
                            <span v-if="allMapping[name].type === 'User'">Benutzer</span>
                            <span v-if="allMapping[name].type === 'Group'">Gruppe</span>
                        </td>
                        <td class="col-3">
                            <OptionBadges :value="allMapping[name].value" v-if="!allMapping[name].global"/>
                        </td>
                        <td class="col-1">
                            <button class="btn btn-sm btn-light float-right" @click="deleteMapping(name)"
                                    v-if="ui.editable && !allMapping[name].global">
                                <span class="material-icons text-danger">delete</span>
                            </button>
                            <button v-if="!editMappingKey && ui.editable && !allMapping[name].global"
                                    class="mr-2 btn btn-sm btn-light float-right"
                                    @click="onEditMapping(name, template.mapping[name])">
                                <span class="material-icons text-dark">edit</span>
                            </button>
                        </td>
                    </template>
                    <!-- Mapping bearbeiten -->
                    <template v-if="name === editMappingKey">
                        <td class="col-3">
                            <input class="form-control form-control-sm" type="text" placeholder=""
                                   v-model="editMappingName">
                        </td>
                        <td class="col-3">
                            <input class="form-control form-control-sm" type="text" placeholder=""
                                   v-model="editMapping.description">
                        </td>
                        <td class="col-2">
                            <select class="custom-select custom-select-sm" v-model="editMapping.type"
                                    @change="onChangeEditType(template.mapping[name])">
                                <option selected value="string">Zeichenkette</option>
                                <option value="array">JSON-Array / JSON-Objekt</option>
                                <option value="ListConfig">Listeninhalt</option>
                                <option value="User">Benutzer</option>
                                <option value="Group">Gruppe</option>
                            </select>
                        </td>
                        <td class="col-3">
                            <!-- String-Wert -->
                            <AutocompleteSelector v-if="['string', 'array', 'User', 'Group'].includes(editMapping.type)"
                                                  :items="editMapping.value ? [editMapping.value] : []"
                                                  :add-only-from-autocomplete="false" :max-items="1"
                                                  :syntax-include="Object.keys(syntaxLoaderLabels())"
                                                  @items-changed="$event.length ? editMapping.value = $event[0] : editMapping.value = ''"/>
                            <select class="custom-select custom-select-sm" v-if="editMapping.type === 'ListConfig'"
                                    v-model="editMapping.value">
                                <option v-for="listConfig in list_configs"
                                        :value="'listConfig|' + listConfig.slug + '[' + listConfig.name + ' - ' + listConfig.slug + ']'">
                                    {{ listConfig.name + ' - ' + listConfig.slug }}
                                </option>
                            </select>
                        </td>
                        <td class="col-1">
                            <button class="btn btn-sm btn-light float-right" @click="updateEditMapping">
                                <span class="material-icons text-success">save</span>
                            </button>
                            <button class="mr-2 btn btn-sm btn-light float-right" @click="cancelEditMapping">
                                <span class="material-icons text-danger">clear</span>
                            </button>
                        </td>
                    </template>
                </tr>
                </tbody>
            </table>
            <table class="table table-sm my-0">
                <tbody>
                <!-- Neues Mapping -->
                <tr class="d-flex" v-if="ui.editable">
                    <td class="col-3">
                        <input class="form-control form-control-sm" type="text" placeholder=""
                               v-model="newMapping.name">
                    </td>
                    <td class="col-3">
                        <input class="form-control form-control-sm" type="text" placeholder=""
                               v-model="newMapping.description">
                    </td>
                    <td class="col-2">
                        <select class="custom-select custom-select-sm" v-model="newMapping.type"
                                @change="newMapping.value = ''">
                            <option selected value="string">Zeichenkette</option>
                            <option value="array">JSON Objekt/Array</option>
                            <option value="ListConfig">Listeninhalt</option>
                            <option value="User">Benutzer</option>
                            <option value="Group">Gruppe</option>
                        </select>
                    </td>
                    <td class="col-3">
                        <AutocompleteSelector v-if="['string', 'array', 'User', 'Group'].includes(newMapping.type)"
                                              :items="newMapping.value ? [newMapping.value] : []"
                                              :add-only-from-autocomplete="false" :max-items="1"
                                              :syntax-include="Object.keys(syntaxLoaderLabels())"
                                              @items-changed="$event.length ? newMapping.value = $event[0] : newMapping.value = ''"/>
                        <select class="custom-select custom-select-sm" v-if="newMapping.type === 'ListConfig'"
                                v-model="newMapping.value">
                            <option v-for="listConfig in list_configs"
                                    :value="'listConfig|' + listConfig.slug + '[' + listConfig.name + ' - ' + listConfig.slug + ']'">
                                {{ listConfig.name + ' - ' + listConfig.slug }}
                            </option>
                        </select>
                    </td>
                    <td class="col-1">
                        <button class="btn btn-sm btn-success float-right" @click="addMapping">
                            <span class="material-icons">add</span>
                        </button>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-12">
                        <small class="text-muted d-block" v-if="ui.editable">Nur "a-z", "0-9" und "_".
                            Kleingeschrieben. Mit Buchstaben beginnnen.</small>
                        <small class="text-muted d-block" v-if="template.type === 'html'">Eventuell werden
                            Datenfelder von Prozessor-Daten überschrieben. Siehe "E-Mail
                            versenden"-Prozessor.</small>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-for="error in (ui.validationErrors.name || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "./CodeEditor";
import {Base64} from 'js-base64';
import OptionBadges from "../utils/OptionBadges";
import GlobalTemplateData from "./partials/GlobalTemplateData";
import AllowedHtmlTags from "./partials/AllowedHtmlTags.vue";
import AutocompleteSelector from "../utils/AutocompleteSelector";

export default {
    components: {
        AutocompleteSelector,
        GlobalTemplateData,
        AllowedHtmlTags,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object
    },
    data() {
        return {
            loading: false,
            error: null,
            errorMessage: '',
            editMappingKey: null,
            editMappingName: null,
            editMapping: {},
            search: '',
            newMapping: {
                name: '',
                description: '',
                type: 'string',
                value: ''
            },
            globalData: {
                app_name: {
                    description: 'Plattform-Name',
                    type: 'string',
                    global: true
                },
                app_description: {
                    description: 'Plattform-Beschreibung',
                    type: 'string',
                    global: true
                },
                app_url: {
                    description: 'Plattform-Url',
                    type: 'string',
                    global: true
                },
                app_image_url: {
                    description: 'URL des Plattform-Logos',
                    type: 'string',
                    global: true
                },
                action_data: {
                    description: 'Assoziatives Array mit den Aktions-Daten als Keys und den Werten. Leer beim Smart-Status-Typ "Eigene Logik".',
                    type: 'array',
                    global: true
                },
                process_name: {
                    description: 'Name der Prozess-Instanz.',
                    type: 'string',
                    global: true
                },
                process_id: {
                    description: 'Id der Prozess-Instanz.',
                    type: 'string',
                    global: true
                },
                process_url: {
                    description: 'Absolute URL zur Prozess-Instanz.',
                    type: 'string',
                    global: true
                },
                process_data: {
                    description: 'Assoziatives Array mit den Prozess-Datenfeldern als Keys und den Werten.',
                    type: 'array',
                    global: true
                },
                process_situation: {
                    description: 'Assoziatives Array mit den Status-Referenznamen als Keys und einem weiteren assoziativen Array mit den Status-Informationen als Werten.',
                    type: 'array',
                    global: true
                },
                action_type_name: {
                    description: 'Name der Aktion, in der die Vorlage genutzt. Leer beim Smart-Status-Typ "Eigene Logik".',
                    type: 'string',
                    global: true
                },
                time_24: {
                    description: 'Uhrzeit im 24-Stunden Format, z.B. "08:45".',
                    type: 'string',
                    global: true
                },
                date_ddmmyyyy: {
                    description: 'Datum im "dd.mm.yyyy"-Format, z.B. "03.10.1980".',
                    type: 'string',
                    global: true
                },
                user_full_name: {
                    description: 'Vor- und Nachname des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".',
                    type: 'string',
                    global: true
                },
                user_first_name: {
                    description: 'Vorname des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".',
                    type: 'string',
                    global: true
                },
                user_last_name: {
                    description: 'Nachname des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".',
                    type: 'string',
                    global: true
                },
                user_email: {
                    description: 'E-Mail des Benutzers. Leer beim Smart-Status-Typ "Eigene Logik".',
                    type: 'string',
                    global: true
                },
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
            'list_configs',
            'relation_types_with_single_process',
            'graphs_output_names',
            'graphs_status_types',
            'environments'
        ]),
        allMapping() {
            return {
                ...this.template.mapping, ...this.globalData
            };
        },
        allMappingKeysSorted() {
            return Object.keys(this.allMapping).filter(ele => ele.includes(this.search)).sort();
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        base64decode: Base64.decode,
        base64encode: Base64.encode,
        addMapping() {
            let name = this.newMapping.name.trim();
            if (name === '') {
                return;
            }
            if (Object.keys(this.template.mapping).includes(name)) {
                this.setError({
                    'code': 422,
                    'message': 'Variablen-Name: Dieser Name existiert bereits im Daten-Mapping.'
                });
                return;
            }
            if (!name.match('^[a-z]+([a-z0-9_]*)')) {
                this.setError({
                    'code': 422,
                    'message': 'Variablen-Name: Nur "a-z", "0-9" und "_". Kleingeschrieben. Mit Buchstaben beginnnen.'
                });
                return;
            }

            this.$emit('update-property', 'mapping', {
                ...this.template.mapping,
                [name]: {
                    type: this.newMapping.type,
                    description: this.newMapping.description,
                    value: this.newMapping.value
                }
            });

            this.newMapping = {
                name: '',
                description: '',
                type: 'string',
                value: ''
            };
        },
        onEditMapping(name, mappingObj) {
            this.editMappingKey = name;
            this.editMappingName = name;
            this.editMapping = {...mappingObj};
        },
        updateEditMapping() {
            if (!this.editMappingName) {
                return;
            }

            let mapping = {...this.template.mapping};

            // Altes Mapping löschen
            if (this.editMappingName !== this.editMappingKey) {
                delete mapping[this.editMappingKey];
            }

            this.$emit('update-property', 'mapping', {
                ...mapping,
                [this.editMappingName]: this.editMapping
            });

            this.cancelEditMapping();
        },
        cancelEditMapping() {
            this.editMappingKey = null;
            this.editMappingName = null;
            this.editMapping = {};
        },
        deleteMapping(name) {
            let mapping = {...this.template.mapping};
            delete mapping[name];

            this.$emit('update-property', 'mapping', mapping);
        },
        onChangeEditType(originalMappingItem) {
            if (originalMappingItem.type === this.editMapping.type) {
                this.editMapping.value = originalMappingItem.value;
            }
            else {
                this.editMapping.value = '';
            }
        }
    },
    watch: {
        newMapping: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        template: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }

};
</script>
