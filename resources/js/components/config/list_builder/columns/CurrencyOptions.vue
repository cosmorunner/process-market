<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="currencyCode">Währung</label>
            <select class="form-control" id="currencyCode" @change="toggleCurrencyCode" :value="currencyCode">
                <option>EUR</option>
                <option>GBP</option>
                <option>CHF</option>
                <option>USD</option>
            </select>
        </div>
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="showDecimals" :checked="showDecimals"
                   @change="toggleShowDecimals">
            <label class="custom-control-label" for="showDecimals">Dezimalstellen</label>
        </div>
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="showCurrencyCode" :checked="showCurrencyCode"
                   @change="toggleShowCurrencyCode">
            <label class="custom-control-label" for="showCurrencyCode">Währungscode</label>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        column: Object,
        supportData: Object,
        locals: Object
    },
    data() {
        return {
            currencyCode: this.column.type_options.currency_code,
            showDecimals: this.column.type_options.show_decimals,
            showCurrencyCode: this.column.type_options.show_currency_code
        };
    },
    methods: {
        toggleCurrencyCode(e) {
            this.$emit('type-options-change', 'currency_code', e.target.value);
        },
        toggleShowDecimals(e) {
            this.$emit('type-options-change', 'show_decimals', e.target.checked);
        },
        toggleShowCurrencyCode(e) {
            this.$emit('type-options-change', 'show_currency_code', e.target.checked);
        },
    }
};
</script>
