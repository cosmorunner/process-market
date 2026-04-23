<template>
    <div>
        <span class="mb-1 d-block">Vorlade-Daten</span>
        <div class="mb-3 border-left border-right" v-if="actionType.inputs.length">
            <Inputs :inputs="actionType.inputs" :edit-mode="false"/>
            <div class="bg-white px-2 py-1 border-bottom">
                <small class="text-muted">Bei einer Initialaktion werden hier keine aufgelösten Syntax-Werte angezeigt.</small>
            </div>
        </div>
        <span class="d-block text-muted" v-else>-</span>

        <span class="mb-1 d-block">Statusregeln</span>
        <div class="mb-3" v-if="actionType.status_rules.length">
            <table class="table mb-0 bg-white border">
                <tbody>
                <template v-for="statusRule in actionType.status_rules">
                    <StatusRule :status-rule="statusRule"
                                :status-type="statusType(statusRule.status_type_id)"
                                :hide-delete-button="true" />
                    <tr class="d-flex">
                        <td class="col-1 border-0 px-2 py-1"></td>
                        <td class="col-11 border-0 px-2 py-1">
                            <a class="pl-2 d-block" data-toggle="collapse"
                               :href="'#states-' + statusRule.status_type_id"
                               aria-expanded="false" :aria-controls="'states-' + statusRule.status_type_id">
                                <small class="text-muted">
                                    <span class="material-icons">keyboard_arrow_down</span> Zustände anzeigen</small>
                            </a>
                            <div class="collapse mt-2" :id="'states-' + statusRule.status_type_id">
                                <StatesTableSimple :states="statusType(statusRule.status_type_id).states"/>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>

        </div>
        <span class="d-block text-muted" v-else>-</span>

        <span class="mb-1 d-block">Prozessoren</span>
        <div v-if="actionType.processors.length">
            <div class="mb-2" v-for="processor in sortedProcessors">
                <DefaultItem :processor="processor"
                             :definition="definition"
                             :action-type="actionType"
                             :edit-mode="false"
                             :show-footer="false"
                />
                <div class="bg-white border" v-if="processor.conditions.length">
                    <small class="p-2 text-muted">Nur ausführen wenn...</small>
                    <ConditionsTable :conditions="processor.conditions" :edit-mode="false" :show-group-labels="false"/>
                </div>
            </div>
        </div>
        <span class="d-block text-muted" v-else>-</span>
    </div>
</template>

<script>

import StatesTableSimple from "./StatesTableSimple";
import DefaultItem from "../config/processors/DefaultItem";
import ConditionsTable from "../config/partials/ConditionsTable";
import StatusRule from "./StatusRule";
import Inputs from "./Inputs";

export default {
    components: {
        Inputs,
        StatusRule,
        DefaultItem,
        StatesTableSimple,
        ConditionsTable
    },
    props: {
        actionType: Object,
        statusTypes: Object | Array,
        definition: Object
    },
    methods: {
        statusType(statusTypeId) {
            for (let i = 0; i < this.statusTypes.length; i++) {
                if (this.statusTypes[i].id === statusTypeId) {
                    return this.statusTypes[i];
                }
            }

            return null;
        },
    },
    computed: {
        sortedProcessors() {
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

            return [...this.actionType.processors].sort((a, b) => (a.sort !== null ? a.sort : sort.indexOf(a.identifier)) - (b.sort !== null ? b.sort : sort.indexOf(b.identifier)));
        },
    }
};
</script>
