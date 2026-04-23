<template>
    <div class="card mb-0 rounded-0 border-left-0 border-right-0">
        <div class="card-header d-flex align-items-center justify-content-between p-2">
            <span>Prozessoren <span class="badge badge-light" v-if="processors.length">{{ processors.length }}</span></span>
            <a :href="configUrl('ActionTypes','Processors', actionTypeId)" class="btn btn-sm btn-outline-primary">
                <span class="material-icons">list_alt</span>
            </a>
        </div>
        <div class="card-body px-2 py-1" v-if="processors.length">
            <ul class="list-group list-group-flush">
                <li class="list-group-item py-1 px-0" v-for="processor in sortedProcessors">
                    <span class="material-icons">{{ processorIcons(processor.identifier) }}</span>
                    <span>{{ processorNames(processor.identifier) }}</span>
                </li>
            </ul>
        </div>
        <div class="card-body px-2 py-1" v-if="!processors.length">
            <span>-</span>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";
import utils from '../../develop-utils';

export default {
    props: {
        actionTypeId: String,
        processors: Array | Object,
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        sortedProcessors: function () {
            let sort = [
                'execute_custom_logic',
                'update_process_meta',
                'delete_access',
                'delete_relation',
                'create_process',
                'create_access',
                'create_e_invoice',
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
                'redirect',
                'delete_process'
            ];

            return [...this.processors].sort((a, b) => (a.sort !== null ? a.sort : sort.indexOf(a.identifier)) - (b.sort !== null ? b.sort : sort.indexOf(b.identifier)));
        },
    },
    methods: {
        ...utils
    }
};
</script>
