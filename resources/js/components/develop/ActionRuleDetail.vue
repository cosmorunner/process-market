<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="showActionTypeDetail(actionType.id)" href="#">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <span class="nav-item nav-link p-2">
                        Aktionsregel
                    </span>
                </div>
            </nav>
            <div class="d-flex align-content-center pr-2 py-1" v-if="ui.editable">
                <button class="btn btn-sm btn-light mr-1" @click="openActionRuleModal(actionType)">
                    <span class="material-icons text-warning">edit</span>
                </button>
                <button class="btn btn-sm btn-light" @click="onDeleteActionRule(actionType.id, actionRule.status_type_id)">
                    <span class="material-icons text-danger">close</span>
                </button>
            </div>
        </div>
        <div class="card mb-3 rounded-0 border-left-0 border-right-0 border-bottom-0">
            <div class="card-header px-1 py-0">
                <small class="text-muted">{{ groupLabels[actionRule.group]}}</small>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <ActionRule :action-rule="actionRule" :status-type="statusType" :onDeleteActionRule="onDeleteActionRule"></ActionRule>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ActionRule from "./ActionRule";

export default {
    components: {ActionRule},
    props: {
        actionRuleId: String
    },
    data(){
        return {
            groupLabels: {
                'group_1': 'Gruppe 1',
                'group_2': 'Gruppe 2',
                'group_3': 'Gruppe 3',
                'group_4': 'Gruppe 4',
                'group_5': 'Gruppe 5',
            }
        }
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'action_rules',
            'definition',
            'ui'
        ]),
        actionRule: function () {
            return this.action_rules.find(ele => ele.id === this.actionRuleId);
        },
        actionType: function () {
            return this.action_types.find(ele => ele.id === this.actionRule.action_type_id);
        },
        statusType: function () {
            return this.status_types.find(ele => ele.id === this.actionRule.status_type_id);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        openActionRuleModal() {
            this.openModal({
                componentName: 'ActionRuleModal',
                data: {
                    statusTypeId: this.statusType.id,
                    actionTypeId: this.actionType.id,
                    actionRuleId: this.actionRule.id
                }
            });
        }
    },
};
</script>
