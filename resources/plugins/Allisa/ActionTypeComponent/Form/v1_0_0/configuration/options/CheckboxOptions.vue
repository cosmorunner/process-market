<template>
    <div>
        <ComputedInputOptions
            :computed-input="computedInput"
            v-on="$listeners"
            :helper-text="helperText"
            :editable="editable"
        />
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
    data() {
        return {
            helperText: '"value" ist "1" wenn die Checkbox markiert ist und "0", wenn die Checkbox nicht markiert ist. "Truthy"-Wert oder "1" zurückgeben, um die Checkbox zu markieren. "Falsy"-Wert oder "0" zurückgeben, um die Checkbox nicht zu markieren.'
        }
    },
    computed: {
        computedInput: function () {
            return this.field.computed_input || '';
        }
    },
};
</script>

