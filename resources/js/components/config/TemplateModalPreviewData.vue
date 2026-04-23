<template>
    <div class="form-group input-group-sm mb-0"
         :style="'overflow-y:scroll;max-height:' + (ui.modal.bodyHeight - 52) + 'px'">
        <!-- Twig -->
        <template v-if="template.type !== 'mustache_list_column'">
            <div class="p-2">
                <span class="font-weight-bold">Testwerte für Daten-Mapping</span>
            </div>
            <table class="table table-sm mb-0 mt-2 d-block" v-if="Object.keys(template.mapping).length">
                <tbody class="d-block">
                <tr v-for="name in Object.keys(template.mapping).sort()" class="d-flex">
                    <td class="col-4">
                        <span>{{ name }}</span> -
                        <small v-if="template.mapping[name].type === 'string'" class="text-muted">Zeichenkette</small>
                        <small v-if="template.mapping[name].type === 'array'" class="text-muted">JSON
                            Objekt/Array</small>
                        <small v-if="template.mapping[name].type === 'ListConfig'"
                               class="text-muted">Listeninhalt</small>
                        <small v-if="template.mapping[name].type === 'User'" class="text-muted">Benutzer</small>
                        <small v-if="template.mapping[name].type === 'Group'" class="text-muted">Gruppe</small>
                        <small v-if="template.mapping[name].type === 'js'" class="text-muted">JSON-Objekt</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm" v-if="template.mapping[name].type === 'string'">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values[name]" placeholder="Zeichenketten-Wert..."
                                   @input="$emit('update-preview-data', name, $event.target.value)">
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example"
                             v-else-if="template.mapping[name].type === 'User'">
                            <button type="button"
                                    :class="{'btn btn-outline-primary': true, 'active': !previewDataset.values[name]}"
                                    @click="$emit('update-preview-data', name, null)">Kein Benutzer
                            </button>
                            <button type="button"
                                    :class="{'btn btn-outline-primary': true, 'active': previewDataset.values[name] === '[[faker.user]]'}"
                                    @click="$emit('update-preview-data', name, '[[faker.user]]')">Automatisch
                                generierter Benutzer
                            </button>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example"
                             v-else-if="template.mapping[name].type === 'Group'">
                            <button type="button"
                                    :class="{'btn btn-outline-primary': true, 'active': !previewDataset.values[name]}"
                                    @click="$emit('update-preview-data', name, null)">Keine Gruppe
                            </button>
                            <button type="button"
                                    :class="{'btn btn-outline-primary': true, 'active': previewDataset.values[name] === '[[faker.group]]'}"
                                    @click="$emit('update-preview-data', name, '[[faker.group]]')">Automatisch
                                generierte Gruppe
                            </button>
                        </div>
                        <CodeEditor v-else :code="JSON.stringify((previewDataset.values[name] || []), null, 4)"
                                    @update-code="onUpdateCode(name, $event)"/>
                        <small v-if="invalidJsonFields.includes(name)" class="opacity-3">
                            <small class="material-icons text-danger">close</small>
                            <small>Ungültiges JSON.</small>
                        </small>
                        <small
                            v-if="template.mapping[name].type === 'ListConfig' && (!Array.isArray(previewDataset.values[name]) || !previewDataset.values[name].every(ele => typeof ele === 'object' && ele !== null))"
                            class="opacity-3">
                            <small class="material-icons text-danger">close</small>
                            <small>Array mit JSON Objekten erforderlich.</small>
                        </small>
                        <small v-else>&nbsp;</small>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-else class="mt-2">
                <div class="alert alert-info mb-0" role="alert">
                    Die Vorlage hat kein Daten-Mapping.
                </div>
            </div>
            <div class="p-2">
                <span class="font-weight-bold">Testwerte für globale Variablen</span>
            </div>
            <table class="table table-sm mb-0 mt-2 d-block">
                <tbody class="d-block">
                <tr class="d-flex">
                    <td class="col-4">
                        <span>action_data</span> - <small class="text-muted">JSON Objekt</small>
                    </td>
                    <td class="col-8">
                        <CodeEditor :code="JSON.stringify((previewDataset.values.action_data || {}), null, 4)"
                                    @update-code="onUpdateCode('action_data', $event.trim() === '' ? '{}' : $event, true)"/>
                        <small v-if="invalidJsonFields.includes('action_data')" class="opacity-3">
                            <small class="material-icons text-danger">close</small>
                            <small>Ungültiges JSON.</small>
                        </small>
                        <small
                            v-if="(Array.isArray(previewDataset.values.action_data) && previewDataset.values.action_data.length) || (!typeof previewDataset.values.action_data === 'object' && previewDataset.values.action_data !== null)"
                            class="opacity-3">
                            <small class="material-icons text-danger">close</small>
                            <small>JSON Objekt erforderlich.</small>
                        </small>
                        <small v-else>&nbsp;</small>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>process_data</span> - <small class="text-muted">JSON Objekt</small>
                    </td>
                    <td class="col-8">
                        <CodeEditor :code="JSON.stringify((previewDataset.values.process_data || {}), null, 4)"
                                    @update-code="onUpdateCode('process_data', $event.trim() === '' ? '{}' : $event, true)"/>
                        <small v-if="invalidJsonFields.includes('process_data')" class="opacity-3">
                            <small class="material-icons text-danger">close</small>
                            <small>Ungültiges JSON.</small>
                        </small>
                        <small
                            v-if="(Array.isArray(previewDataset.values.process_data) && previewDataset.values.process_data.length) || (!typeof previewDataset.values.process_data === 'object' && previewDataset.values.process_data !== null)"
                            class="opacity-3">
                            <small class="material-icons text-danger">close</small>
                            <small>JSON Objekt erforderlich.</small>
                        </small>
                        <small v-else>&nbsp;</small>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>process_situation</span> - <small class="text-muted">JSON Objekt</small>
                    </td>
                    <td class="col-8">
                        <CodeEditor :code="JSON.stringify((previewDataset.values.process_situation || {}), null, 4)"
                                    @update-code="onUpdateCode('process_situation', $event.trim() === '' ? '{}' : $event, true)"/>
                        <div class="d-flex justify-content-between">
                            <div>
                                <small v-if="invalidJsonFields.includes('process_situation')" class="opacity-3">
                                    <small class="material-icons text-danger">close</small>
                                    <small>Ungültiges JSON.</small>
                                </small>
                                <small
                                    v-if="(Array.isArray(previewDataset.values.process_situation) && previewDataset.values.process_situation.length)  || (!typeof previewDataset.values.process_situation === 'object' && previewDataset.values.process_situation !== null)"
                                    class="opacity-3">
                                    <small class="material-icons text-danger">close</small>
                                    <small>JSON Objekt erforderlich.</small>
                                </small>
                                <small v-else></small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>app_name</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.app_name" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'app_name', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>app_description</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.app_description" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'app_description', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>app_url</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.app_url" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'app_url', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>app_image_url</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.app_image_url" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'app_image_url', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>process_name</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.process_name" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'process_name', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>process_id</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.process_id" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'process_id', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>process_url</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.process_url" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'process_url', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>action_type_name</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.action_type_name" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'action_type_name', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>time_24</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.time_24" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'time_24', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>date_ddmmyyyy</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.date_ddmmyyyy" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'date_ddmmyyyy', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>user_full_name</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.user_full_name" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'user_full_name', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>user_first_name</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.user_first_name" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'user_first_name', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>user_last_name</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.user_last_name" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'user_last_name', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4">
                        <span>user_email</span> - <small class="text-muted">Zeichenkette</small>
                    </td>
                    <td class="col-8">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                   :value="previewDataset.values.user_email" placeholder="Standard-Wert nutzen..."
                                   @input="$emit('update-preview-data', 'user_email', $event.target.value.trim() === '' ? null : $event.target.value.trim(), true)">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
        <!-- Mustache -->
        <template v-else>
            <div class="p-2">
                <span class="font-weight-bold">Testwerte für eine Prozessliste</span>
                <span class="d-block"><small class="text-danger material-icons">priority_high</small><small
                    class="text-muted">Mit dem Auswählen einer Liste werden die Testwerte für die Prozessliste überschrieben.</small></span>
            </div>
            <div class="p-2">
                <select class="form-control form-control-sm mt-2" id="test_values_process_list"
                        v-model="previewDatasetListConfigSlug" @change="refreshProcessListDataset($event.target.value)"
                        :disabled="!ui.editable">
                    <option :value="''">Prozessliste wählen um verfügbare Datenfeld-Aliases zu laden...</option>
                    <option :value="listConfig.id" v-for="listConfig in sortedListConfigs">
                        {{ listConfig.name }} - {{ listConfig.slug }}
                    </option>
                </select>
            </div>
            <table class="table table-sm mb-0 mt-2 d-block" v-if="Object.keys(template.mapping).length">
                <tbody class="d-block">
                <tr class="d-flex">
                    <td class="col-4">
                        <small class="text-muted">JSON-Objekt</small>
                    </td>
                    <td class="col-8">
                        <CodeEditor :code="JSON.stringify((previewDataset.values['process_list'] || []), null, 4)"
                                    :watch-code-prop="true" @update-code="onUpdateCode('process_list', $event)"/>
                        <p class="my-2">
                            <span class="material-icons text-primary">info</span>
                            <span>Dieses JSON-Objekt steht beim "Daten-Mapping" mit der Variable "row" zur Verfügung. Es repräsentiert eine Listenzeile.</span>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
        <div v-for="error in (ui.validationErrors.name || [])">
            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "./CodeEditor";
import OptionBadges from "../utils/OptionBadges";
import AutocompleteSelector from "../utils/AutocompleteSelector.vue";

export default {
    components: {
        AutocompleteSelector,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object,
        previewDataset: Object | Array
    },
    data() {
        return {
            loading: false,
            error: null,
            errorMessage: '',
            invalidJsonFields: [],
            listConfigColumnAliases: [],
            previewDatasetListConfigSlug: ''
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'list_configs'
        ]),
        sortedListConfigs() {
            return [...this.list_configs].sort((a, b) => a.name > b.name ? 1 : -1);
        },
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onUpdateCode(name, code, clearPreviewDataOnEmpty) {
            let parsed = [];
            this.invalidJsonFields = this.invalidJsonFields.filter(ele => ele !== name);

            try {
                parsed = JSON.parse(code);
            } catch (e) {
                this.invalidJsonFields = [
                    ...this.invalidJsonFields,
                    name
                ];
                return;
            }

            this.$emit('update-preview-data', name, parsed, clearPreviewDataOnEmpty);
        },
        refreshProcessListDataset(listConfigId) {
            this.loadSelectItems(listConfigId);
        },
        loadSelectItems(listConfigId) {
            let that = this;

            if (!listConfigId) {
                return;
            }

            that.listConfigColumnAliases = [];

            axios.get(this.ui.urls.list_support.replace('-listConfigId-', listConfigId) + '?parts=select').then(function (response) {
                let aliases = response.data.select.map(ele => ele.alias || '').filter(ele => ele !== '');
                let data = {};

                for (let i = 0; i < aliases.length; i++) {
                    data[aliases[i]] = '';
                }

                that.$emit('update-preview-data', 'process_list', data, false);
            }).catch(function (error) {
                that.loading = false;
                console.log(error);
            });
        }
    },
    watch: {
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
