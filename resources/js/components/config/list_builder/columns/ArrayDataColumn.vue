<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr v-if="placeholder">
        <span v-if="placeholder" class="badge badge-light">Platzhalter: {{ placeholder }}</span>
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
        placeholder() {
            return this.column.type_options.placeholder || '';
        }
    },
};
</script>
