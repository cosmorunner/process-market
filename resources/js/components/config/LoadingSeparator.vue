<template>
    <div class="my-2">
        <div v-if="!ui.loading && !ui.errorCode" class="separator"></div>
        <div v-if="ui.loading" class="loading-separator"></div>
        <div v-if="!ui.loading && ui.errorCode" class="loading-error-separator"></div>
        <div v-if="ui.errorCode && ui.errorMessage" class="card-header d-flex justify-content-between p-2">
            <span class="text-danger">{{ ui.errorMessage }}</span>
            <button class="btn btn-sm py-0 text-danger" @click="clearError"><span class="material-icons">clear</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        ui: Object,
        clearError: Function
    }
};
</script>
