<template>
    <div>
        <DefaultColumn :column="column"
                       :used-column-aliases="usedColumnAliases"
                       :alias-labels="aliasLabels"/>
        <hr/>
        <span v-if="type === 'progress_bar'" class="badge badge-light">Fortschritts-Balken</span>
        <span v-if="type === 'icons'" class="badge badge-light">Icons</span>
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
        type() {
            return this.column.type_options.type;
        },
    }
};
</script>
