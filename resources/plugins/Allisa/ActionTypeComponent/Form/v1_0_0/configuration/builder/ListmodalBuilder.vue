<template>
    <div>
        <DefaultBuilderHeader :field="field"></DefaultBuilderHeader>
        <template v-if="field.width > 2">
            <hr class="my-1" v-if="shouldDisplayHr"/>
            <div class="row">
                <div class="col">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge badge-light" v-if="field.list_config && field.list_config.id">Liste: {{ listConfigName }}</span>
                            <span class="badge badge-light"
                                  v-if="field.value_mapping && Object.keys(field.value_mapping).length">Wert-Mapping: {{
                                    field.value_mapping.filter(ele => ele).join(', ')
                                }}</span>
                            <span class="badge badge-light" v-if="field.mapping && Object.keys(field.mapping).length">Weiteres-Mapping: {{
                                    Object.keys(field.mapping).join(', ')
                                }}</span>
                            <span class="badge badge-light" v-if="(field.css_classes || '').length">CSS</span>
                            <span class="badge badge-light" v-if="field.highlighted">Hervorgehoben</span>
                            <span class="badge badge-light"
                                  v-if="field.auto_mapping_on_single_list_entry">Auto-Mapping</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>

import DefaultBuilderHeader from "./DefaultBuilderHeader";

export default {
    components: {DefaultBuilderHeader},
    props: {
        field: Object,
        actionTypeInputs: Array,
        actionTypeOutputs: Array,
        processTypeOutputs: Array,
        definition: Object
    },
    computed: {
        listConfigName() {
            let listConfig = this.definition.list_configs.find(ele => ele.id === this.field.list_config.id);

            if (!listConfig) {
                return '?';
            }

            return listConfig.name;
        },
        shouldDisplayHr() {
            return (this.field.label || this.field.helper_text)
                && (this.field.list_config && this.field.list_config.id)
                || this.field.css_classes || (this.field.mapping && Object.keys(this.field.mapping).length) || (this.field.value_mapping && Object.keys(this.field.value_mapping).length) || this.field.highlighted || this.field.auto_mapping_on_single_list_entry;
        }
    }
};
</script>
