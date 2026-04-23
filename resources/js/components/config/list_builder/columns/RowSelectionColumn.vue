<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
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
};
</script>
