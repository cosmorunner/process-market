<template>
    <div class="modal-footer py-2 d-flex justify-content-between">
        <div class="w-50">
            <span v-if="errorCode" class="text-danger">{{errorMessage}}</span>
        </div>
        <div>
            <button v-if="!loading" type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                Abbrechen
            </button>
            <button v-if="!onlyCancel && !loading" type="button"
                    class="btn btn-sm btn-success"
                    @click="$emit('save')"
                    :disabled="disabled">{{saveLabel}}
            </button>
            <button v-if="!onlyCancel && loading"
                    class="btn btn-warning btn-sm align-items-center justify-content-between" disabled>
                <span class="material-icons">more_horiz</span> Laden...
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        saveLabel: {
            required: true,
            type: String
        },
        onlyCancel: {
            required: false,
            default: false
        },
        loading: {
            required: true,
            type: Boolean
        },
        errorCode: {
            required: true,
            type: Number|null
        },
        errorMessage: {
            rquired: true,
            type: String|null
        },
        saveDisabled: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    computed: {
        disabled: function(){
            return this.saveDisabled
        }
    }
};
</script>

