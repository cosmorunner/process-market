<template>
    <div>
        <div class="form-group">
            <div class="d-flex justify-content-between">
                <label class="mb-0">Computed Input - JavaScript Editor</label>
            </div>
            <div>
                <CodeEditor :code="computedInput" @code-changed="onComputedInputChanged" :max-length="4000" :editable="editable"/>
                <div v-if="computedInput.length > 3999">
                    <small class="text-danger">Wert zu lang!</small>
                </div>
                <div v-if="computedInput.length <= 3999">
                    <small class="text-muted">{{ helperText }}</small>
                    <a href="/docs/de/action-components#allisa-form-computed-input" target="_blank">
                        <span class="material-icons">help_outline</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CodeEditor from "./CodeEditor";

export default {
    components: {CodeEditor},
    props: {
        computedInput: String | null,
        helperText: {
            required: false,
            default: "\"String\" oder \"Number\" zurückgeben um Wert zu akzeptieren (\"return ...\").\n\"Null\" zurückgeben um Wert zu entfernen."
        },
        editable: Boolean
    },
    methods: {
        onComputedInputChanged(code) {
            this.$emit('property-change', 'computed_input', code);
        }
    },
    mounted() {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }
};
</script>
