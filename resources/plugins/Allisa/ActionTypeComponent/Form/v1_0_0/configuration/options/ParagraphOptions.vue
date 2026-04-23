<template>
    <div>
        <div class="form-group mb-2">
            <label for="alert" class="mb-0">Text</label>
            <textarea class="form-control form-control-sm" rows="4" id="alert" v-model="field.default" :readonly="!editable"></textarea>
            <OptionBadgesWithText :value="field.default" display-block hide-on-empty/>
        </div>
        <ComputedInputOptions :computed-input="computedInput" v-on="$listeners" :editable="editable"/>
    </div>
</template>

<script>

import ComputedInputOptions from "./partials/ComputedInputOptions";
import OptionBadgesWithText from "../../../../../../../js/components/utils/OptionBadgesWithText.vue";

export default {
    components: {
        OptionBadgesWithText,
        ComputedInputOptions
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
};
</script>

