<template>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" v-if="processor">
            <ModalHeader :title="addProcessor ? 'Neuer Prozessor' : processorNames(processor.identifier)"/>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="identifier">Typ</label>
                    <select class="form-control" id="identifier" name="identifier" @change="onIdentifierChange"
                            :value="processor.identifier" :disabled="!ui.editable">
                        <option value="execute_action">{{ processorNames('execute_action') }}</option>
                        <option value="tag_action">{{ processorNames('tag_action') }}</option>
                        <option value="copy_artifact">{{ processorNames('copy_artifact') }}</option>
                        <option value="trigger_task">{{ processorNames('trigger_task') }}</option>
                        <option value="send_push_message">{{ processorNames('send_push_message') }}</option>
                        <option value="trigger_connector">{{ processorNames('trigger_connector') }}</option>
                        <option value="create_document">{{ processorNames('create_document') }}</option>
                        <option value="execute_custom_logic">{{ processorNames('execute_custom_logic') }}</option>
                        <option value="send_email">{{ processorNames('send_email') }}</option>
                        <option value="create_e_invoice">{{ processorNames('create_e_invoice') }}</option>
                        <option value="trigger_event">{{ processorNames('trigger_event') }}</option>
                        <option value="display_flash_message">{{ processorNames('display_flash_message') }}</option>
                        <option value="create_process">{{ processorNames('create_process') }}</option>
                        <option value="delete_process">{{ processorNames('delete_process') }}</option>
                        <option value="update_process_meta">{{ processorNames('update_process_meta') }}</option>
                        <option value="create_relation">{{ processorNames('create_relation') }}</option>
                        <option value="delete_relation">{{ processorNames('delete_relation') }}</option>
                        <option value="redirect">{{ processorNames('redirect') }}</option>
                        <option value="delete_access">{{ processorNames('delete_access') }}</option>
                        <option value="create_access">{{ processorNames('create_access') }}</option>
                        <option :value="plugin.full_namespace_with_version" v-for="plugin in customProcessorPlugins">{{ plugin.name }}</option>
                    </select>
                    <div v-for="error in (ui.validationErrors.identifier || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-0">Pflichtausführung</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" :disabled="!ui.editable"
                               :checked="processor.required" @click="toggleRequired">
                        <label class="custom-control-label" for="customSwitch1"></label>
                    </div>
                    <small class="text-muted">Die Aktionsausführung wird abgebrochen wenn Daten für den Prozessor
                        fehlen.<br/>
                        Bei einer optionalen Ausführung und fehlenden Daten wird der Prozessor übersprungen.</small>
                </div>
                <hr class="my-3">
                <component :is="componentName"
                           :options="processor.options"
                           :processors="actionType.processors"
                           :roles="roles"
                           :action-type="actionType"
                           :outputs="outputs"
                           :relation-types="relation_types"
                           :templates="templates"
                           :environments="environments"
                           :editable="ui.editable"
                           @option-change="onOptionChange"
                />
            </div>
            <ModalFooter :ui="ui" @save="save" :save-label="'Speichern'" />
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import utils from "../../../config-utils";
import ModalHeader from "../ModalHeader";
import ModalFooter from "../ModalFooter";

export default {
    components: {
        ModalHeader,
        ModalFooter,
        CopyArtifactOptions: () => import('./CopyArtifactOptions.vue'),
        CreateAccessOptions: () => import('./CreateAccessOptions.vue'),
        CreateDocumentOptions: () => import('./CreateDocumentOptions.vue'),
        CreateEInvoiceOptions: () => import('./CreateEInvoiceOptions.vue'),
        CreateProcessOptions: () => import('./CreateProcessOptions.vue'),
        CreateRelationOptions: () => import('./CreateRelationOptions.vue'),
        DeleteAccessOptions: () => import('./DeleteAccessOptions.vue'),
        DeleteProcessOptions: () => import('./DeleteProcessOptions.vue'),
        DeleteRelationOptions: () => import('./DeleteRelationOptions.vue'),
        DisplayFlashMessageOptions: () => import('./DisplayFlashMessageOptions.vue'),
        ExecuteActionOptions: () => import('./ExecuteActionOptions.vue'),
        ExecuteCustomLogicOptions: () => import('./ExecuteCustomLogicOptions.vue'),
        RedirectOptions: () => import('./RedirectOptions.vue'),
        SendEmailOptions: () => import('./SendEmailOptions.vue'),
        SendPushMessageOptions: () => import('./SendPushMessageOptions.vue'),
        TagActionOptions: () => import('./TagActionOptions.vue'),
        TriggerConnectorOptions: () => import('./TriggerConnectorOptions.vue'),
        TriggerEventOptions: () => import('./TriggerEventOptions.vue'),
        TriggerTaskOptions: () => import('./TriggerTaskOptions.vue'),
        UpdateProcessMetaOptions: () => import('./UpdateProcessMetaOptions.vue')
    },
    props: {
        confirmLabel: String
    },
    data() {
        return {
            loaded: true,
            processor: null,
            originalProcessor: null,
            actionType: null,
            addProcessor: false
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'outputs',
            'roles',
            'templates',
            'relation_types',
            'action_types',
            'environments'
        ]),
        customProcessorPlugins() {
            return this.ui.plugins.external.customProcessor;
        },
        componentName() {
            // Custom plugin.
            if(this.processor.identifier.includes('/')) {
                let namespaceParts = this.processor.identifier.split('@')[0]
                let version = this.processor.identifier.split('@')[1] || 'latest';
                let namespace = namespaceParts.split('/')[0]
                let identifier = namespaceParts.split('/')[1]

                identifier = identifier.replaceAll('-', '');
                version = version.replaceAll('.', '_')

                // e.g. lubw-customprocessor-shapeimport-v1_0_0-configuration-templateoptions;
                return namespace + '-customprocessor-' + identifier + '-v' + version + '-configuration-templateoptions'

            } else {
                // e.g. UpdateProcessMetaOptions
                return this.processor.identifier.split('_').map(ele => ele.charAt(0).toUpperCase() + ele.slice(1)).join('') + 'Options';
            }
        }
    },
    methods: {
        ...mapActions(reduxActions),
        ...utils,
        save() {
            let method = this.addProcessor ? 'StoreProcessor' : 'UpdateProcessor';
            this.patchDefinition(method, this.processor).then(this.closeModal).catch(() => {
            });
        },
        onIdentifierChange(e) {
            let identifier = e.target.value;
            if (identifier === this.processor.identifier) {
                this.processor = this.originalProcessor;
            }
            else {
                // In case of an identifier with "/" was selected, a custom processor plugin was selected, we get the concrete namespace from the "data-namespace" attribute
                // and load the values from the plugin data.
                let defaultOptions = this.defaultProcessorOptions(identifier);

                if(identifier.includes('/')) {
                    defaultOptions = this.customProcessorPlugins.find(item => item.full_namespace_with_version === identifier).data.default_options
                }

                this.processor = {
                    ...this.processor,
                    identifier,
                    options: defaultOptions
                };
            }
        },
        onOptionChange(property, value) {
            this.processor = {
                ...this.processor,
                options: {
                    ...this.processor.options,
                    [property]: value
                }
            };
        },
        toggleRequired() {
            this.processor = {
                ...this.processor,
                required: !this.processor.required
            };
        }
    },
    watch: {
        processor: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    mounted() {
        let processor = this.ui.modal.data.processor;
        // Neuen Prozessor anlegen
        if (processor === null) {
            processor = {
                action_type_id: this.ui.modal.data.actionType.id,
                conditions: [],
                identifier: 'update_process_meta',
                options: this.defaultProcessorOptions('update_process_meta'),
                required: true
            };

            this.addProcessor = true;
        }

        this.actionType = {...this.ui.modal.data.actionType};
        this.processor = {...processor};
        this.originalProcessor = {...processor};
    }
};
</script>
