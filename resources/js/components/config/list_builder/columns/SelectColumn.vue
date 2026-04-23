<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr v-if="customDataName || defaultValue">
        <span v-if="customDataName" class="badge badge-light">Daten-Name: {{ customDataName }}</span>
        <span v-if="defaultValue" class="badge badge-light">Standard: {{ defaultValue }}</span>
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
        customDataName(){
            return this.column.type_options.custom_data_name || '';
        },
        defaultValue(){
            return this.column.type_options.default || '';
        }
    },
    methods: {

    }
};
</script>
