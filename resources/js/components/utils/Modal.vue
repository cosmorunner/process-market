<template>
    <div>
        <div class="modal" id="genericModal" tabindex="-1" role="dialog" data-backdrop="static"
             aria-labelledby="genericModal"
             aria-hidden="true">
            <component :is="modal.component"
                       :data="modal.data"
                       :on-confirm="modal.onConfirm"
                       :loading="loading"
                       :error-code="errorCode"
                       :error-message="errorMessage"
                       :validation-errors="validationErrors"
                       :clear-error="clearError"
                       v-on="$listeners"
            >
            </component>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        modal: {
            required: true,
            type: Object
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
