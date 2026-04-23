<template>
    <div class="d-flex justify-content-between">
        <div>
            <span class="text-danger" v-if="errorCode != null">{{ errorMessage }}</span>
        </div>
        <div>
            <!-- Speichern Button wenn nicht gesendet wird und es entweder einen Validierungsfehler oder keinen Fehler gibt -->
            <button v-if="!saving && !saved && (errorCode === 422 || errorCode === null)" type="button" @click="onSave"
                    :disabled="disabled"
                    class="btn btn-sm btn-success float-right">{{ label }}
            </button>
            <!-- Loading Icon -->
            <button v-if="saving && !errorCode" type="button" class="btn btn-sm btn-outline-success float-right"
                    disabled>
                <img src="/img/loading.gif" :alt="''" width="18"/>
            </button>
            <!-- Success -->
            <button v-if="saved" type="button" class="btn btn-sm btn-outline-success float-right" disabled>
                <span class="material-icons">done</span>
                <span>Alles gespeichert & synchronisiert</span>
            </button>
            <!-- Error -->
            <button v-if="errorCode !== null && errorCode !== 422" type="button"
                    class="btn btn-sm btn-outline-danger float-right"
                    disabled>Error
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        saving: Boolean,
        saved: Boolean,
        errorMessage: String | null,
        errorCode: Number | null,
        disabled: {
            required: false,
            default: false
        },
        label: {
            required: false,
            default: 'Speichern'
        },
        onSave: Function
    }
};
</script>
