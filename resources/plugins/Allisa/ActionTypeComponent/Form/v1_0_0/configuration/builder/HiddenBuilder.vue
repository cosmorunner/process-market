<template>
    <div>
        <template v-if="field.width > 2">
            <div class="row px-2 py-1" v-if="field.computed_input && field.computed_input.length">
                <div class="col">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge badge-light" v-if="field.computed_input && field.computed_input.length">Computed Input</span>
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
    }
};
</script>
