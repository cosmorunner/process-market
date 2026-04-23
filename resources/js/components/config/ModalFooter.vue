<template>
    <div class="modal-footer py-2 d-flex justify-content-between">
        <div class="w-75 m-0">
            <span v-if="ui.errorCode" class="text-danger">{{ ui.errorMessage }}</span>
        </div>
        <div class="w-25 m-0 d-flex justify-content-end">
            <button v-if="!ui.loading && !hideCancelButton" type="button" class="btn btn-sm btn-light"
                    data-dismiss="modal" @click="$emit('cancel')">
                {{ ui.editable ? 'Abbrechen' : 'Schließen' }}
            </button>
            <button v-if="!ui.loading && !ui.errorCode && ui.editable" type="button" class="btn btn-sm btn-success"
                    @click="$emit('save')" :disabled="disabled">
                {{ saveLabel }}
            </button>
            <button v-if="ui.loading && !ui.errorCode"
                    class="btn btn-warning btn-sm align-items-center justify-content-between" disabled>
                <span class="material-icons">more_horiz</span> Laden...
            </button>
            <button v-if="ui.errorCode" class="btn btn-danger btn-sm align-items-center justify-content-between"
                    disabled>
                <span class="material-icons mr-2">priority_high</span>
                <span> {{ui.errorCode === 422 ? 'Eingabefehler' : 'Error'}}</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        saveLabel: String,
        ui: Object,
        saveDisabled: {
            required: false,
            type: Boolean,
            default: false
        },
        hideCancelButton: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        disabled: function () {
            return this.saveDisabled;
        }
    }
};
</script>
