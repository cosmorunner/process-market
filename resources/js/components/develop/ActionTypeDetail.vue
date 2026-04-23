<template>
    <div>
        <div class="d-flex justify-content-between border-bottom align-content-center">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p-2 active mouse-pointer" v-on:click="clearElementDetails">
                        <span class="material-icons text-white">arrow_back</span>
                    </a>
                    <span class="nav-item nav-link p-2">
                        <span>{{ actionType.name  }}</span>
                    </span>
                </div>
            </nav>
            <div class="d-flex align-content-center pr-2 py-1" v-if="ui.editable">

                <button class="btn btn-sm btn-light" @click="openActionTypeModal(actionType)">
                    <span class="material-icons text-warning">edit</span>
                </button>
            </div>
        </div>
        <div class="detail-content panel-content-max-vh overflow-auto">
            <div class="card rounded-0 border-0">
                <div class="card-body px-2 py-1">
                    <span class="text-muted d-block">Referenz-Name: {{ actionType.reference }}</span>
                </div>
            </div>
            <ActionType :action-type="actionType" :hide-footer="true" :hide-header="true" :editable="ui.editable"
                        :use-rules-collapse="false"/>
            <Inputs :inputs="actionType.inputs" :action-type-id="actionType.id" :editable="ui.editable"></Inputs>
            <Outputs :outputs="actionType.outputs"
                     :action-type-id="actionType.id"
                     :editable="ui.editable"
                     update-method="UpdateActionTypeOutput"
                     store-method="StoreActionTypeOutput"
                     store-bulk-method="StoreActionTypeOutputBulk"
                     update-bulk-method="UpdateActionTypeOutputBulk"
            />
            <ProcessorsList :processors="actionType.processors" :action-type-id="actionType.id"/>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ActionRule from "./ActionRule";
import StatusRule from "./StatusRule";
import ActionType from "./ActionType";
import Inputs from "./Inputs";
import Outputs from "./Outputs";
import ProcessorsList from "./ProcessorsList";

export default {
    components: {
        ProcessorsList,
        ActionType,
        StatusRule,
        ActionRule,
        Inputs,
        Outputs
    },
    props: {
        actionTypeId: String
    },
    computed: {
        ...mapGetters([
            'status_types',
            'action_types',
            'definition',
            'simulation',
            'ui'
        ]),
        actionType() {
            return this.action_types.find(ele => ele.id === this.actionTypeId);
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        openActionTypeModal(actionType) {
            this.openModal({
                componentName: 'ActionTypeModal',
                data: {
                    position: null,
                    actionTypeId: actionType.id
                }
            });
        }
    },
};
</script>
