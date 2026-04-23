<template>
    <div>
        <div v-if="!ui.loading && !ui.errorCode" class="separator"></div>
        <div v-if="ui.loading" class="loading-separator"></div>
        <div v-if="!ui.loading && ui.errorCode" class="loading-error-separator"></div>
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
