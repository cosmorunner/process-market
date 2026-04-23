<template>
    <span class="badge badge-light font-weight-normal text-primary border"
          style="font-size: 90%; white-space: normal; text-align: left;"
          :data-toggle="disableTooltip ? '' : 'tooltip'" data-placement="top" :title="disableTooltip ? '' : tooltip"><span
        class="material-icons" v-if="icon">{{ icon }}</span> {{ label }}</span>
</template>

<script>
export default {
    props: {
        disableTooltip: {
            default: false,
            type: Boolean
        },
        icon: String,
        tooltip: String,
        label: String
    },
    mounted() {
        $('[data-toggle="tooltip"]').tooltip();
    }
};
</script>
