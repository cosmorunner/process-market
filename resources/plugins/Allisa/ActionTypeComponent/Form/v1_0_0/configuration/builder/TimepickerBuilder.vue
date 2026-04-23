<template>
    <div>
        <DefaultBuilderHeader :field="field"></DefaultBuilderHeader>
        <template v-if="field.width > 2">
            <hr class="my-1" v-if="shouldDisplayHr"/>
            <div class="row">
                <div class="col">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge badge-light" v-if="(field.css_classes || '').length">CSS</span>
                            <span class="badge badge-light" v-if="field.highlighted">Hervorgehoben</span>
                        </div>
                        <div>
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
        processTypeOutputs: Array
    },
    computed: {
        shouldDisplayHr() {
            return (this.field.label || this.field.helper_text) && (this.field.css_classes || this.field.highlighted);
        }
    }
};
</script>
