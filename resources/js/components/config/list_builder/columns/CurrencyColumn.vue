<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"
                       :locals="locals"/>
        <hr v-if="showDecimals || showCurrencyCode">
        <span v-if="showDecimals" class="badge badge-light">Dezimalstellen</span>
        <span v-if="showCurrencyCode" class="badge badge-light">Währungscode</span>
        <span v-if="currencyCode" class="badge badge-light">{{ currencyCode }}</span>
    </div>
</template>

<script>

import DefaultColumn from "./DefaultColumn";

export default {
    components: {DefaultColumn},
    props: {
        column: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object,
        locals: Object
    },
    computed: {
        currencyCode: function () {
            return this.column.type_options.currency_code || '';
        },
        showDecimals: function () {
            return this.column.type_options.show_decimals || '';
        },
        showCurrencyCode: function () {
            return this.column.type_options.show_currency_code || '';
        }
    }
};
</script>
