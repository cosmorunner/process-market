<template>
    <div class="modal-footer py-2 d-flex justify-content-between">
        <div class="w-50">
            <span v-if="ui.errorCode" class="text-danger">{{ ui.errorMessage }}</span>
        </div>
        <div>
            <button v-if="!ui.loading" type="button" class="btn btn-sm btn-light" data-dismiss="modal" @click="$emit('cancel')">
                {{ ui.editable ? 'Abbrechen' : 'Schliessen' }}
            </button>
            <button v-if="!ui.loading && !ui.errorCode && ui.editable" :disabled="saveDisabled"
                    type="button" class="btn btn-sm btn-success" @click="onSave">{{ saveLabel }}
            </button>
            <button v-if="ui.loading && !ui.errorCode"
                    class="btn btn-warning btn-sm align-items-center justify-content-between" disabled>
                <span class="material-icons">more_horiz</span> Laden...
            </button>
            <button v-if="ui.errorCode"
                    class="btn btn-danger btn-sm align-items-center justify-content-between" disabled>
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
        onSave: Function,
        ui: Object,
        saveDisabled: {
            type: Boolean,
            default: false
        }
    }
};
</script>

