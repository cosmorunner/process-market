<template>
    <div>
        <div class="form-group mb-2">
            <label for="rows" class="mb-0">Zeilen</label>
            <input type="number" class="form-control form-control-sm" :readonly="!editable"
                   id="rows" v-model="field.rows" aria-describedby="rows" min="1" step="1" @input="onRowsInputs"/>
        </div>
        <ComputedInputOptions :computed-input="computedInput" v-on="$listeners" :editable="editable"/>
    </div>
</template>

<script>

import ComputedInputOptions from "./partials/ComputedInputOptions";

export default {
    components: {
        ComputedInputOptions,
    },
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
    methods: {
        onRowsInputs(e) {
            let value = e.target.value.trim() === '' ? '' : Math.abs(+e.target.value.trim());
            this.$emit('property-change', 'rows', value);
        }
    }
};
</script>
