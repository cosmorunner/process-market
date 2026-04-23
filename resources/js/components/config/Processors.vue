<template>
    <div class="row" v-if="actionType">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary btn-sm" @click="openAddProcessorModal" v-if="ui.editable">Prozessor anlegen</button>
                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-sm btn-light" disabled v-if="!manualSortEnabled">
                        <span>Autom. Reihenfolge</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-light" disabled v-if="manualSortEnabled">
                        <span>Manuelle Reihenfolge</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-link" @click="resetToAutoSort" v-if="manualSortEnabled && ui.editable">
                        <span>Zurücksetzen</span>
                    </button>
                </div>
            </div>
            <Draggable v-model="draggableProcessors" class="row processors mt-2" group="draggableProcessors"
                       handle=".drag-handle-processor" v-bind="dragOptions" v-if="draggableProcessors.length">
                <template v-for="processor in draggableProcessors">
                    <div class="col-12 mb-2">
                        <DefaultItem :processor="processor" :definition="definition" :action-type="actionType"/>
                    </div>
                </template>
            </Draggable>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../config-utils';
import DefaultItem from "./processors/DefaultItem";
import Draggable from "vuedraggable";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        DefaultItem,
        Draggable
    },
    props: {
        actionType: Object,
    },
    data() {
        return {
            dragOptions: {
                animation: 0,
                disabled: false,
                ghostClass: "drag-clone"
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'definition',
            'graphs'
        ]),
        draggableProcessors: {
            get() {
                return this.sortedProcessors;
            },
            set(newProcessors) {
                let sorted = [
                    ...newProcessors.map((ele, index) => (
                        {
                            ...ele,
                            sort: index
                        }
                    ))
                ];

                this.patchDefinition('UpdateActionType', {
                    ...this.actionType,
                    processors: sorted
                }).catch(() => {
                });
            }
        },
        sortedProcessors() {
            let sort = [
                'execute_custom_logic',
                'update_process_meta',
                'delete_access',
                'delete_relation',
                'create_process',
                'create_access',
                'create_relation',
                'create_document',
                'copy_artifact',
                'send_push_message',
                'send_email',
                'trigger_connector',
                'trigger_event',
                'execute_action',
                'trigger_task',
                'display_flash_message',
                'tag_action',
                'redirect',
                'delete_process',
                'create_e_invoice'
            ];

            return [...this.actionType.processors].sort((a, b) => (a.sort !== null ? a.sort : sort.indexOf(a.identifier)) - (b.sort !== null ? b.sort : sort.indexOf(b.identifier)));
        },
        manualSortEnabled() {
            return this.actionType.processors.reduce((carry, processor) => carry || processor.sort !== null, false);
        }
    },
    methods: {
        ...mapActions(reduxActions),
        ...utils,
        openAddProcessorModal() {
            this.openModal({
                componentName: 'EditorProcessorModal',
                data: {
                    processor: null,
                    actionType: this.actionType
                }
            });
        },
        resetToAutoSort() {
            let processors = [...this.actionType.processors].map(function (ele) {
                let processor = {...ele};
                processor.sort = null;

                return processor;
            });

            this.patchDefinition('UpdateActionType', {
                ...this.actionType,
                processors: processors
            }).catch(() => {
            });
        }
    },
    mounted() {
        // Here we already load the process versions, so they are already loaded then opening a processor modal.
        if (!this.graphs.length) {
            this.fetchUserGraphs();
        }
    }
};
</script>

<style>
/*noinspection CssUnusedSymbol*/
.drag-clone {
    opacity: 0.7;
}
</style>
