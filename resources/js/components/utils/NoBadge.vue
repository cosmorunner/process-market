<template>
    <span>{{rawValue}}</span>
</template>

<script>
export default {
    props: {
        disableTooltip: {
            default: false,
            type: Boolean
        },
        rawValue: String
    }
}
</script>
