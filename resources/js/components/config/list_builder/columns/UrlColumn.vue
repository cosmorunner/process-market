<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr/>
        <span v-if="label && !column.data && !column.alias" class="badge badge-light">Link-Label: {{ label }}</span>
        <span v-if="url" class="badge badge-light">{{ defaultUrls[url] || 'Eigene Url' }}</span>
        <span v-if="target" class="badge badge-light">{{ target === '_self' ? 'Gleicher Tab' : 'Neuer Tab' }}</span>
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
    data() {
        return {
            defaultUrls: {
                '/processes/$': 'Prozess-Link'
            }
        };
    },
    computed: {
        label() {
            return this.column.type_options.label
        },
        url() {
            return this.column.type_options.url;
        },
        target() {
            return this.column.type_options.target;
        },
        hide: function () {
            return this.column.type_options.hide || [];
        }
    }
};
</script>
