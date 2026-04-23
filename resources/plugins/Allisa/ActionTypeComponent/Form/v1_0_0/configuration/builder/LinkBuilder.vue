<template>
    <div>
        <DefaultBuilderHeader :field="field"></DefaultBuilderHeader>
        <template v-if="field.width > 2">
            <hr class="my-1" v-if="shouldDisplayHr"/>
            <div class="row">
                <div class="col">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span v-if="field.style" class="badge badge-light">{{ labels(field.style) }}</span>
                            <span v-if="field.target" class="badge badge-light">{{
                                    field.target === '_self' ? 'Gleicher Tab' : 'Neuer Tab'
                                }}</span>
                            <span class="badge badge-light" v-if="field.computed_input && field.computed_input.length">Computed Input</span>
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
            return (this.field.label || this.field.helper_text)
                && (this.field.css_classes || (this.field.computed_input && this.field.computed_input.length)
                    || this.field.highlighted || this.field.style || this.field.target);
        }
    },
    methods: {
        labels(type) {
            return {
                'link': 'Link',
                'button': 'Button',
                'button-block': 'Block-Button',
            }[type] || '?';
        }
    },
};
</script>
