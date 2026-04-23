<template>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <ModalHeader :title="'Ausführungs-Bedingungen'"/>
            <div class="modal-body py-2">
                <div class="container-fluid">
                    <div class="row d-flex">
                        <div class="col">
                            <span class="d-block mb-2"><span class="material-icons">flash_on</span> Nur ausführen wenn...</span>
                            <div class="mb-3">
                                <ConditionsTable :conditions="[...processor.conditions]" @delete-item="onDeleteItem" :editable="ui.editable"/>
                            </div>
                            <ConditionsAdd :action-outputs="actionTypeOutputs"
                                           :process-outputs="outputs"
                                           :conditions="[...processor.conditions]"
                                           @add-condition="onConditionAdd"
                                           v-if="ui.editable"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" @save="save" :save-label="'Speichern'"/>
        </div>
    </div>
</template>

<script>

import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import ConditionsTable from "../partials/ConditionsTable";
import ConditionsAdd from "../partials/ConditionsAdd";
import utils from "../../../config-utils";
import {reduxActions} from "../../../store/develop-and-config";
import {mapActions, mapGetters} from "vuex";

export default {
    components: {
        ConditionsTable,
        ConditionsAdd,
        ModalHeader,
        ModalFooter,
    },
    props: {
        data: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            processor: {...this.data.processor},
            actionTypeOutputs: [...this.data.actionTypeOutputs]
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'outputs'
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        save() {
            this.patchDefinition('UpdateProcessor', this.processor).then(this.closeModal).catch(() => {
            });
        },
        onConditionAdd(condition) {
            this.processor = {
                ...this.processor,
                conditions: [...this.processor.conditions, condition]
            };
        },
        onDeleteItem(item) {
            let conditions = [...this.processor.conditions].filter(function (ele) {
                return JSON.stringify(ele) !== JSON.stringify(item);
            });

            this.processor = {
                ...this.processor,
                conditions: [...conditions]
            };
        }
    }
};
</script>
