<template>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <ModalHeader :title="'Bedingungen'"/>
            <div class="modal-body py-2">
                <div class="container">
                    <div class="row d-flex">
                        <div class="col">
                        <span class="d-block mb-2"><span
                            class="material-icons">flash_on</span> Nur erfüllt, wenn...</span>
                            <div class="mb-3">
                                <ConditionsTable :conditions="[...permission.conditions]" @delete-item="onDeleteItem"
                                                 :editable="ui.editable"/>
                            </div>
                            <ConditionsAdd :syntax-loader-include="syntaxLoaderInclude"
                                           :conditions="[...permission.conditions]" @add-condition="onConditionAdd"
                                           v-if="ui.editable"/>
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter :on-save="onSave" @cancel="onCancel" :ui="ui" :save-label="'Übernehmen'"/>
        </div>
    </div>
</template>

<script>

import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import ConditionsTable from "../config/partials/ConditionsTable";
import ConditionsAdd from "../config/partials/ConditionsAdd";
import utils from "../../config-utils";
import {reduxActions} from "../../store/develop-and-config";
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
            permission: {...this.data.permission},
            syntaxLoaderInclude: [
                'process.outputs',
                'process.status'
            ]
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'outputs'
        ]),
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onConditionAdd(condition) {
            this.permission = {
                ...this.permission,
                conditions: [
                    ...this.permission.conditions,
                    condition
                ]
            };
        },
        onDeleteItem(item) {
            let conditions = [...this.permission.conditions].filter(function (ele) {
                return JSON.stringify(ele) !== JSON.stringify(item);
            });

            this.permission = {
                ...this.permission,
                conditions: [...conditions]
            };
        },
        onCancel() {
            this.$emit('open-role');
        },
        onSave() {
            this.$emit('save-conditions', this.permission);
        }
    }
};
</script>
