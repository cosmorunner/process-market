<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr/>
        <span v-if="mapping && Object.keys(mapping).length" class="badge badge-light">Icon / Wert Mapping</span>
        <span v-if="colors && Object.keys(colors).length" class="badge badge-light">Farbe / Wert Mapping</span>
        <span v-if="size === 'small' " class="badge badge-light">Größe: Klein</span>
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
        mapping: function () {
            return this.column.type_options.mapping;
        },
        colors: function () {
            return this.column.type_options.colors;
        },
        size: function () {
            return this.column.type_options.size || 'normal';
        },
        hide: function () {
            return this.column.type_options.hide || [];
        }
    }
};
</script>
