<template>
    <div>
        <DefaultColumn :column="column" :used-column-aliases="usedColumnAliases" :alias-labels="aliasLabels"/>
        <hr v-if="templateId">
        <span v-if="templateId" class="badge badge-light">{{ templateName }}</span>
    </div>
</template>

<script>

import DefaultColumn from "./DefaultColumn";
import {mapGetters} from "vuex";

export default {
    components: {DefaultColumn},
    props: {
        column: Object,
        usedColumnAliases: Array | Object,
        aliasLabels: Object
    },
    computed: {
        ...mapGetters([
            'templates',
        ]),
        templateId() {
            return this.column.type_options.template_id || '';
        },
        templateName() {
            let template = this.templates.find((template) => template.id === this.templateId);

            if (!template) {
                return 'Fehlende Vorlage';
            }

            return template.name;
        }
    }
};
</script>
