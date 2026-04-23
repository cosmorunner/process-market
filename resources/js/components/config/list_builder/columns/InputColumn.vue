<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr v-if="placeholder || type || customDataName || defaultValue">
        <span v-if="type" class="badge badge-light">{{ labelForType(type) }}</span>
        <span v-if="placeholder" class="badge badge-light">Platzhalter: {{ placeholder }}</span>
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
        placeholder () {
            return this.column.type_options.placeholder || '';
        },
        type() {
            return this.column.type_options.type || 'text';
        },
        customDataName(){
            return this.column.type_options.custom_data_name || '';
        },
        defaultValue(){
            return this.column.type_options.default || '';
        }
    },
    methods: {
        labelForType(type) {
            return {
                text: 'Text',
                email: 'E-Mail',
                number: 'Zahl'
            }[type] || 'Unkown'
        }
    }
};
</script>
