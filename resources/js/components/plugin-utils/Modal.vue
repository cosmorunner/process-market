<template>
    <div v-if="modal">
        <div class="modal" id="genericModal" tabindex="-1" role="dialog" data-backdrop="static"
             aria-labelledby="genericModal"
             aria-hidden="true">
            <div class="modal-dialog modal-lg no-pointer" role="document">
                <div class="modal-content">
                    <ModalHeader :title="modal.title || 'Optionen'"/>
                    <div class="modal-body py-2">
                        <component :is="modal.component"
                                   :data="JSON.parse(JSON.stringify(modal.data))"
                                   :loading="loading"
                                   :error-code="errorCode"
                                   :error-message="errorMessage"
                                   :validation-errors="validationErrors"
                                   :clear-error="clearError"
                                   v-on="$listeners"
                                   :editable="editable"
                                   @update-parent-data="updateChildData"
                        >
                        </component>
                    </div>
                    <ModalFooter :loading="loading"
                                 :error-code="errorCode"
                                 :error-message="errorMessage"
                                 :editable="editable"
                                 @save="modal.onConfirm(onConfirmData)"
                                 :save-label="'Speichern'"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import ModalHeader from "./ModalHeader";
import ModalFooter from "./ModalFooter";

export default {
    components: {
        ModalHeader,
        ModalFooter
    },
    props: {
        modal: {
            required: true,
            type: Object | null
        },
        loading: {
            required: true,
            type: Boolean
        },
        errorCode: {
            required: true,
            type: Number | null
        },
        errorMessage: {
            required: true,
            type: String | null
        },
        validationErrors: {
            required: true,
            type: Object | Array
        },
        clearError: {
            required: true,
            type: Function
        },
        editable: {
            default: true,
            type: Boolean
        }
    },
    data() {
        return {
            onConfirmData: {...this.modal.data}
        };
    },
    methods: {
        updateChildData(data) {
            this.onConfirmData = {
                ...data
            };
        }
    },
    created() {
        $(document).one('hidden.bs.modal', '#genericModal', () => this.$emit('close-modal'));
    },
    mounted() {
        $('#genericModal').modal('show');
    },
    beforeDestroy() {
        $('#genericModal').modal('hide');
    }
};
</script>
