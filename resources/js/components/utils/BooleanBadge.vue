<template>
    <span class="text-uppercase">{{ displayValue }}</span>
</template>

<script>
export default {
    props: {
        disableTooltip: {
            default: false,
            type: Boolean
        },
        rawValue: String | Boolean
    },
    computed: {
        displayValue() {
            if (typeof this.rawValue === 'boolean') {
                return this.rawValue ? 'TRUE' : 'FALSE';
            }
            else {
                return this.rawValue.split('|')[1] || '';
            }
        }
    }
};
</script>
