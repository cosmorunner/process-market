<template>
    <div>
        <button v-if="!error && upToDate && !saving" class="btn btn-outline-success btn-sm" disabled>
            {{ locals.all_saved }}
        </button>
        <button v-if="!upToDate && !saving" class="btn btn-success btn-sm" @click="$emit('save-list')">
            {{ locals.save }}
        </button>
        <button v-if="saving" class="btn btn-warning btn-sm">{{ locals.saving }}</button>
    </div>
</template>

<script>
export default {
    props: {
        upToDate: Boolean,
        saving: Boolean,
        error: Boolean | null,
        locals: Object
    },
};
</script>
