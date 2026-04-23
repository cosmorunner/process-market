<template>
    <div>
        <div class="modal mouse-initial" id="genericModal" tabindex="-1" role="dialog" data-backdrop="static"
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
        modal: Object,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        validationErrors: Object | Array,
        clearError: Function
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
