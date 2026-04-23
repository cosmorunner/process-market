<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr v-if="dateFormat || hide.length">
        <span v-if="dateFormat" class="badge badge-light">Datumsformat</span>
        <span v-if="hide.length" class="badge badge-light">Ausblenden-Regel</span>
    </div>
</template>

<script>

import DefaultColumn from "./DefaultColumn";

export default {
    components: {DefaultColumn},
    props: {
        column: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object
    },
    computed: {
        dateFormat: function () {
            return this.column.type_options.date_format || '';
        },
        hide: function () {
            return this.column.type_options.hide || [];
        }
    }
};
</script>
