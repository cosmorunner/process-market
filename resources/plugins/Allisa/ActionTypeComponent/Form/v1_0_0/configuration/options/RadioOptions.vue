<template>
    <div>
        <ItemsOptions :field="field" @property-change="onPropertyChange" :editable="editable"/>
        <VariableOptions :field="field" @property-change="onPropertyChange" :editable="editable"/>
        <div class="pt-2 mb-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="optionsAmongEachOther"
                       @click="toggleDisplayAsRows" :checked="displayAsRows" :disabled="!editable">
                <label class="custom-control-label" for="optionsAmongEachOther">Optionen untereinander anzeigen</label>
            </div>
        </div>
        <ComputedInputOptions :computed-input="computedInput" v-on="$listeners" :editable="editable"/>
    </div>
</template>

<script>

import ItemsOptions from "./partials/ItemsOptions";
import ComputedInputOptions from "./partials/ComputedInputOptions";
import ListConfigOptions from "./partials/ListConfigOptions.vue";
import VariableOptions from "./partials/VariableOptions.vue";

export default {
    components: {
        ListConfigOptions,
        ComputedInputOptions,
        ItemsOptions,
        VariableOptions
    },
    props: {
        field: Object,
        inputOutputNames: Array,
        definition: Object,
        editable: Boolean
    },
    computed: {
        computedInput() {
            return (this.field.computed_input || '').trim();
        },
        displayAsRows() {
            return this.field.display_as_rows || false
        }
    },
    methods: {
        onPropertyChange(property, value) {
            this.$emit('property-change', property, value);
        },
        toggleDisplayAsRows(e){
            this.$emit('property-change', 'display_as_rows', e.target.checked);
        }
    }
};
</script>

