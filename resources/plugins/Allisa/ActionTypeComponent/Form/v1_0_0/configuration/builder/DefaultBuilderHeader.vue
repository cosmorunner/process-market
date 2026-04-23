<template>
    <div class="row">
        <div class="col">
            <span class="d-block text-truncate" v-if="field.label">{{ field.label }}</span>
            <template v-else>
                &nbsp;
            </template>
            <small class="text-muted d-block text-truncate" v-if="field.width > 2 && field.helper_text">{{ field.helper_text }}</small>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        field: Object
    },
};
</script>

