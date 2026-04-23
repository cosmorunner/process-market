<template>
    <div class="rounded-0 processor card h-100">
        <div class="card-header px-2 py-1 d-flex justify-content-between border-primary">
            <span
                :class="'text-truncate flex-grow-1 disable-user-select ' +  (editMode ? 'text-primary hover-pointer' : 'text-dark')"
                @click="editMode ? onOpenOptionsModal(processor) : () => {}">
                <span class="material-icons">{{ processorIcon || 'help' }}</span>
                <span>{{ processorNames(processor.identifier) }}</span>
                <span class="text-muted"
                      v-if="(processor.options.multiple || {}).value || ''"> - Mehrfach-Ausführung</span>
                <span class="text-muted" v-if="!processor.required"> - Optional</span>
                <span class="text-muted" v-if="['create_process', 'create_document'].includes(processor.identifier)">
                    <span>- ID: {{ processor.id.substring(0, 4) }}</span>
                </span>
            </span>
            <button v-if="editMode && ui.editable" class="btn btn-sm btn-light text-danger p-0" @click="deleteProcessor(processor.id)">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div :class="'card-body p-2 ' + (editMode ? 'hover-pointer' : '')"
             @click="editMode ? onOpenOptionsModal(processor) : () => {}">
            <component :is="componentName" :options="processor.options" :definition="definition"/>
        </div>
        <div v-if="showFooter" class="card-footer px-2 py-1">
            <div class="d-flex justify-content-between">
                <div class="text-nowrap">
                    <button class="btn btn-sm btn-light p-0 px-1 text-muted" @click="handleOpenConditions(processor)">
                        <span :class="'material-icons ' + ((processor.conditions || []).length ? 'text-primary' : '')">flash_on</span>
                    </button>
                </div>
                <div>
                    <button class="btn btn-sm btn-light p-0 px-1 ml-1 drag-handle-processor"
                            v-if="['trigger_connector', 'execute_custom_logic', 'update_process_meta'].includes(processor.identifier) && ui.editable">
                        <span class="material-icons text-muted">swap_vert</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import ConditionsModal from "./ConditionsModal";
import CopyArtifact from './CopyArtifact';
import CreateAccess from './CreateAccess';
import CreateDocument from './CreateDocument';
import CreateEInvoice from "./CreateEInvoice.vue";
import CreateProcess from './CreateProcess';
import CreateRelation from './CreateRelation';
import DeleteAccess from './DeleteAccess';
import DeleteProcess from './DeleteProcess';
import DeleteRelation from './DeleteRelation';
import DisplayFlashMessage from './DisplayFlashMessage';
import ExecuteAction from './ExecuteAction';
import ExecuteCustomLogic from './ExecuteCustomLogic';
import Redirect from './Redirect';
import SendEmail from './SendEmail';
import SendPushMessage from './SendPushMessage';
import TagAction from './TagAction';
import TriggerConnector from './TriggerConnector';
import TriggerEvent from './TriggerEvent';
import TriggerTask from './TriggerTask';
import UpdateProcessMeta from './UpdateProcessMeta';
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../../store/develop-and-config";
import utils from "../../../config-utils";

export default {
    components: {
        ConditionsModal,
        CopyArtifact,
        CreateAccess,
        CreateDocument,
        CreateEInvoice,
        CreateProcess,
        CreateRelation,
        DeleteAccess,
        DeleteProcess,
        DeleteRelation,
        DisplayFlashMessage,
        ExecuteAction,
        ExecuteCustomLogic,
        Redirect,
        SendEmail,
        SendPushMessage,
        TagAction,
        TriggerConnector,
        TriggerEvent,
        TriggerTask,
        UpdateProcessMeta
    },
    props: {
        processor: Object,
        definition: Object,
        actionType: Object,
        editMode: {
            default: true,
            type: Boolean
        },
        showFooter: {
            default: true,
            type: Boolean
        }
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        customProcessorPlugins() {
            return this.ui.plugins.external.customProcessor;
        },
        componentName() {
            // Custom plugin.
            if (this.processor.identifier.includes('/')) {
                let namespaceParts = this.processor.identifier.split('@')[0];
                let version = this.processor.identifier.split('@')[1] || 'latest';
                let namespace = namespaceParts.split('/')[0];
                let identifier = namespaceParts.split('/')[1];

                identifier = identifier.replaceAll('-', '');
                version = version.replaceAll('.', '_');

                // e.g. lubw-customprocessor-shapeimport-v1_0_0-configuration-template;
                return namespace + '-customprocessor-' + identifier + '-v' + version + '-configuration-template';

            }
            else {
                // e.g. UpdateProcessMeta
                return this.processor.identifier.split('_').map(ele => ele.charAt(0).toUpperCase() + ele.slice(1)).join('');
            }
        },
        processorIcon() {
            if (this.processor.identifier.includes('/')) {
                return this.customProcessorPlugins.find(item => item.full_namespace_with_version === this.processor.identifier).data.icon;
            }

            return this.processorIcons(this.processor.identifier);
        }
    },
    methods: {
        ...mapActions(reduxActions),
        ...utils,
        onOpenOptionsModal(processor) {
            this.openModal({
                componentName: 'EditorProcessorModal',
                data: {
                    processor: processor,
                    actionType: this.actionType
                }
            });
        },
        deleteProcessor(id) {
            let payload = {
                action_type_id: this.actionType.id,
                id: id
            };

            this.patchDefinition('DeleteProcessor', payload).then(this.closeModal).catch(() => {
            });
        },
        handleOpenConditions(processor) {
            this.openModal({
                componentName: 'ConditionsModal',
                data: {
                    processor: processor,
                    actionTypeOutputs: this.actionType.outputs
                }
            });
        },
    }
};
</script>
