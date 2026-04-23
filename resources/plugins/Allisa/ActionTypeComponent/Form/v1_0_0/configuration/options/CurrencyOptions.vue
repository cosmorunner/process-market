<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="currency">Währung</label>
            <select class="form-control" id="currency" v-model="field.currency" :disabled="!editable">
                <option>EUR</option>
                <option>GBP</option>
                <option>CHF</option>
                <option>USD</option>
            </select>
        </div>
        <div class="form-group mb-2">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="showDecimals" @click="toggleShowDecimals"
                       :checked="showDecimals" :disabled="!editable">
                <label class="custom-control-label" for="showDecimals">Dezimalstellen</label>
            </div>
            <small class="text-muted">
                Es können zwei Nachkommastellen angegeben werden.
            </small>
        </div>
        <div class="form-group mb-2">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="showCurrencyCode"
                       @click="toggleShowCurrencyCode" :checked="showCurrencyCode" :disabled="!editable">
                <label class="custom-control-label" for="showCurrencyCode">Währungscode</label>
            </div>
            <small class="text-muted">
                Der Währungscode wird links vom Input angezeigt.
            </small>
        </div>
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
        },
        showDecimals() {
            return this.field.showDecimals || true;
        },
        showCurrencyCode() {
            return this.field.showCurrencyCode || true;
        }
    },
    methods: {
        toggleShowDecimals(e) {
            this.$emit('property-change', 'showDecimals', e.target.checked);
        },
        toggleShowCurrencyCode(e) {
            this.$emit('property-change', 'showCurrencyCode', e.target.checked);
        }
    }
};
</script>