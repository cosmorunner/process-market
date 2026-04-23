<template>
    <div>
        <ComputedInputOptions :computed-input="computedInput" v-on="$listeners" :editable="editable"/>
    </div>
</template>

<script>

import ComputedInputOptions from "./partials/ComputedInputOptions";

export default {
    components: {ComputedInputOptions},
    props: {
        field: Object,
        inputOutputNames: Array,
        definition: Object,
        editable: Boolean
    },
    computed: {
        computedInput: function () {
            return (this.field.computed_input || '').trim();
        }
    },
};
</script>
