<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="showActionTypeDetail(actionType.id)" href="#">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <span class="nav-item nav-link p-2">
                        Statusregel
                    </span>
                </div>
            </nav>
            <div class="d-flex align-content-center pr-2 py-1" v-if="ui.editable">
                <button class="btn btn-sm btn-light mr-1" @click="openStatusRuleModal(actionType)">
                    <span class="material-icons text-warning">edit</span>
                </button>
                <button class="btn btn-sm btn-light" @click="onDeleteStatusRule(actionType.id, statusRule.status_type_id)">
                    <span class="material-icons text-danger">close</span>
                </button>
            </div>
        </div>
        <StatusRule :status-rule="statusRule" :status-type="statusType"
                    :onDeleteStatusRule="onDeleteStatusRule"></StatusRule>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import StatusRule from "./StatusRule";

export default {
    components: {StatusRule},
    props: {
        statusRuleId: String
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'status_rules',
            'ui'
        ]),
        statusRule() {
            return this.status_rules.find(ele => ele.id === this.statusRuleId);
        },
        actionType() {
            return this.action_types.find(ele => ele.id === this.statusRule.action_type_id);
        },
        statusType() {
            return this.status_types.find(ele => ele.id === this.statusRule.status_type_id);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        openStatusRuleModal() {
            this.openModal({
                componentName: 'StatusRuleModal',
                data: {
                    statusTypeId: this.statusType.id,
                    actionTypeId: this.actionType.id,
                    statusRuleId: this.statusRule.id
                }
            });
        }
    },
};
</script>
