<template>
    <div>
        <MultipleExecution :multiple-value="(options.multiple || {}).value || ''" :outputs="outputs"
                           :action-type="actionType" :editable="ui.editable"
                           @multiple-value-change="onMultipleValueChange"/>
        <hr/>
        <AutocompleteSelector :items="options.to" :icon="'group'" :label="'Empfänger'" :action-type="actionType"
                              :max-items="multipleOptions.value ? 1 : 15" :add-only-from-autocomplete="false"
                              :outputs-from-actiontype-only="true" :dropdown-width="640"
                              :pipe-include="['roles', 'environment_groups', 'environment_users']"
                              :syntax-include="['action.outputs', 'process.outputs', 'auth', 'groups', 'users', 'reference.outputs']"
                              :editable="ui.editable" @items-changed="$emit('option-change', 'to', $event)"/>
        <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Nur 1 Eintrag möglich. JSON-Array
            Wert erforderlich. CC und BCC stehen nicht zur Verfügung.</small>
        <AutocompleteSelector :items="options.cc" :icon="'group'" :label="'CC'" :action-type="actionType"
                              :max-items="15" :dropdown-width="640" :add-only-from-autocomplete="false"
                              :outputs-from-actiontype-only="true"
                              :pipe-include="['roles', 'environment_groups', 'environment_users']"
                              :syntax-include="['action.outputs', 'process.outputs', 'auth', 'groups', 'users', 'reference.outputs']"
                              :editable="ui.editable" v-if="!multipleOptions.value"
                              @items-changed="$emit('option-change', 'cc', $event)"/>
        <AutocompleteSelector :items="options.bcc" :icon="'group'" :label="'BCC'" :action-type="actionType"
                              :max-items="15" :dropdown-width="640" :add-only-from-autocomplete="false"
                              :outputs-from-actiontype-only="true" :pipe-include="['roles']"
                              :syntax-include="['action.outputs', 'process.outputs', 'auth', 'groups', 'users', 'reference.outputs']"
                              :editable="ui.editable" v-if="!multipleOptions.value"
                              @items-changed="$emit('option-change', 'bcc', $event)"/>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Betreff</label>
            <textarea class="form-control" rows="1" @change="$emit('option-change', 'subject', $event.target.value)"
                      :readonly="!ui.editable" v-bind:value="options.subject"></textarea>
            <OptionBadgesWithText :value="options.subject" display-block hide-on-empty/>
            <small class="text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array Syntax-Werten wird
                der Wert des Iterations-Indexes genutzt.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector :action-type="actionType" :dropdown-width="640" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'process.meta', 'action.outputs', 'reference.outputs', 'date', 'system']"
                              v-if="ui.editable" @selected="appendSubject($event)"/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="template">HTML-Vorlage</label>
            <select class="form-control" id="template" name="template" @change="templateChanged"
                    :disabled="!ui.editable" :value="options.template">
                <optgroup label="Vorlagen">
                    <option v-for="template in htmlTemplates" :value="'template|' + template.id">
                        Vorlage - {{ template.name }}
                    </option>
                </optgroup>
                <optgroup label="Aktions-Datenfeld" v-if="actionType.outputs.length">
                    <option v-for="output in sortedActionTypeOutputs"
                            :value="'[[action.outputs.' + output.name + '((Aktions-Daten - ' + output.name +  '))]]'">
                        Aktions-Datenfeld - {{ output.name }}
                    </option>
                </optgroup>
            </select>
        </div>
        <div class="row d-flex">
            <div :class="(multipleOptions.value ? 'col-11' : 'col-12')">
                <AutocompleteSelector :items="options.attachments" :icon="'attach_file'" :label="'Anhänge'"
                                      :action-type="actionType" :max-items="5" :dropdown-width="640"
                                      :outputs-from-actiontype-only="true" :pipe-include="['environment_variables']"
                                      :syntax-include="['action_type_processors','action.outputs', 'process.outputs', 'reference.outputs']"
                                      :editable="ui.editable"
                                      @items-changed="$emit('option-change', 'attachments', $event)"/>
            </div>
            <div class="col-1 align-self-end" v-if="multipleOptions.value">
                <button :class="'mb-2 btn btn-sm btn-outline-primary ' + (extractFromArrayAttachments ? 'active' : '')"
                        @click="onToggleExtractArrayIndexAttachments" :disabled="!ui.editable" data-toggle="tooltip"
                        data-placement="top"
                        title="Bei JSON-Array Syntax-Werten den Wert des Iterations-Indexes nutzen.">
                    <span class="material-icons">read_more</span>
                </button>
            </div>
        </div>
        <div class="form-group mb-2 mt-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="priority" @click="togglePriority"
                       :disabled="!ui.editable" :checked="options.priority == 1">
                <label class="custom-control-label" for="priority">Hohe Priorität</label>
            </div>
            <small class="text-muted">E-Mails mit hoher Priorität ignorieren die E-Mail-Benachrichtigung Abmeldung des
                Benutzers.</small>
        </div>
        <div v-if="createProcessProcessors.length">
            <label class="mb-0">Prozessor-Daten in Vorlage übernehmen</label>
            <table class="table table-sm" v-if="Object.keys(options.mapping || {}).length">
                <tbody>
                <tr v-for="(syntaxValue, key) in (options.mapping || {})" class="d-flex">
                    <td class="col-4">{{ key }}</td>
                    <td class="col-6">
                        <OptionBadges :value="syntaxValue"/>
                    </td>
                    <td class="col-2">
                        <button class="float-right btn btn-sm btn-light" @click="onDeleteData(key)">
                            <span class="material-icons text-danger">delete</span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-4">
                    <input type="text" v-model="newKey" class="form-control form-control-sm"
                           placeholder="Name...(Nur a-z, 0-9, Unterstrich)"/>
                </div>
                <div class="col-6">
                    <AutocompleteSelector :action-type="actionType" :items="newValue ? [newValue] : []"
                                          :syntax-include="['action.processors']" :max-items="1" :dropdown-width="640"
                                          @items-changed="$event.length ? newValue = $event[0] : newValue = ''"/>
                </div>
                <div class="col-2">
                    <button class="btn btn-sm btn-outline-success" @click="onAddData">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
            <small class="d-block text-muted mb-2">Vorlagen-Datensatz ← Wert. Kleingeschrieben, nur a-z, 0-9 und
                "_".</small>
            <small class="d-block text-muted mb-2">Datensätze überschreiben evtl. bestehende Daten in Vorlage.</small>
            <small class="d-block text-muted" v-if="multipleOptions.value">Mehrfachausführung: Bei JSON-Array
                Syntax-Werten wird der Wert
                des Iterations-Indexes genutzt.</small>
        </div>
    </div>
</template>

<script>

import MultipleExecution from "./MultipleExecution";
import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import OptionBadges from "../../utils/OptionBadges";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";

export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector,
        AutocompleteSelector,
        OptionBadges,
        MultipleExecution
    },
    props: {
        options: Object,
        outputs: Object | Array,
        actionType: Object,
        roles: Object | Array,
        templates: Object | Array,
        environments: Object | Array
    },
    data() {
        return {
            newKey: '',
            newValue: ''
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'processors',
            'ui'
        ]),
        sortedActionTypeOutputs() {
            return [...this.actionType.outputs].sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1);
        },
        createProcessProcessors() {
            return this.processors.filter(ele => ele.identifier === 'create_process');
        },
        htmlTemplates() {
            return this.templates.filter(ele => ele.type === 'html').sort((a, b) => a.name.localeCompare(b.name));
        },
        multipleOptions() {
            return this.options.multiple || {};
        },
        extractFromArrayAttachments() {
            return (this.multipleOptions.extract_from_array || {}).attachments || false;
        }
    },
    methods: {
        ...utils,
        templateChanged(e) {
            let parts = this.getSyntaxParts(e.target.value);

            if (e.target.value.startsWith('[[action.outputs')) {
                this.$emit('option-change', 'template', parts.original);
                this.$emit('option-change', 'template_name', parts.label);

                return;
            }

            let template = this.templates.find(ele => ele.id === parts.key);

            if (template) {
                this.$emit('option-change', 'template', 'template|' + template.id);
                this.$emit('option-change', 'template_name', template.name);
            }
        },
        onAddData() {
            if (!/^[a-z\d_]+$/.test(this.newKey)) {
                return;
            }

            let mapping = {
                ...this.options.mapping,
                [this.newKey]: this.newValue
            };

            this.$emit('option-change', 'mapping', mapping);

            this.newKey = '';
            this.newValue = '';
        },
        onDeleteData(key) {
            let mapping = {...this.options.mapping};
            delete mapping[key];
            this.$emit('option-change', 'mapping', mapping);
        },
        onMultipleValueChange(value) {
            this.$emit('option-change', 'multiple', {
                value: value,
                extract_from_array: {
                    attachments: false
                }
            });

            this.$emit('option-change', 'to', this.options.to.length > 1 ? [this.options.to[0]] : this.options.to);
            this.$emit('option-change', 'cc', []);
            this.$emit('option-change', 'bcc', []);
        },
        onToggleExtractArrayIndexAttachments() {
            let multiple = {...this.multipleOptions};
            let extractFromArrayAttachments = {...multiple.extract_from_array || {}}.attachments || false;

            this.$emit('option-change', 'multiple', {
                ...multiple,
                extract_from_array: {
                    ...multiple.extract_from_array,
                    attachments: !extractFromArrayAttachments
                }
            });
        },
        appendSubject(item) {
            let appended = this.options.subject === null ? item.value_with_label : this.options.subject + item.value_with_label;

            this.$emit('option-change', 'subject', appended);
        },
        togglePriority() {
            this.$emit('option-change', 'priority', this.options.priority === 0 ? 1 : -1);
        },
    }
};
</script>
