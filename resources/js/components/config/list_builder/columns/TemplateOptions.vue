<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="buttonType">Vorlage</label>
            <select class="form-control" id="buttonType" @change="onTemplateIdChange" :value="templateId"
                    :disabled="!editable">
                <option value="">Bitte Vorlage wählen...</option>
                <option :value="template.id" v-for="template in sortedTemplates">{{ template.name }}</option>
            </select>
        </div>
        <hr class="mt-3">
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    computed: {
        ...mapGetters([
            'templates',
        ]),
        templateId: function () {
            return this.column.type_options.template_id || '';
        },
        sortedTemplates() {
            return [...this.templates].filter(ele => ele.type === 'mustache_list_column').sort((a, b) => a.name.localeCompare(b.name));
        }
    },
    methods: {
        onTemplateIdChange: function (e) {
            this.$emit('type-options-change', 'template_id', e.target.value);
        },

    }
};
</script>
