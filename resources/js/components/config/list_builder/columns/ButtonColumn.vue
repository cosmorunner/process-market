<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr/>
        <span v-if="action === 'link'" class="badge badge-light">Link</span>
        <span v-if="action === 'mapping'" class="badge badge-light">Mapping</span>
        <span v-if="action === 'post_request'" class="badge badge-light">POST-Request</span>
        <span v-if="!column.data && !column.alias && column.type_options.label" class="badge badge-light">Button-Label: {{ column.type_options.label }}</span>
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
        aliasLabels: Object,
    },
    computed: {
        hide() {
            return this.column.type_options.hide || [];
        },
        action() {
            return this.column.type_options.action || null;
        }
    }
};
</script>
