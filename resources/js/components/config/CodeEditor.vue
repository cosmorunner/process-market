<template>
    <div>
        <prism-editor class="my-editor" :style="{ 'max-height': maxHeight + 'px' }"
                      v-model="codeString"
                      :highlight="highlighter"
                      :ignore-tab-key="false"
                      line-numbers
                      :tabSize="4"
                      :readonly="!editable"
                      @blur="$emit('blur', codeString)"
        ></prism-editor>
        <input v-if="enableHiddenInput" type="hidden" :name="hiddenInputName" :value="codeString"/>
    </div>
</template>

<script>

import {PrismEditor} from 'vue-prism-editor';
import 'vue-prism-editor/dist/prismeditor.min.css';
import {highlight, languages} from 'prismjs/components/prism-core';
import 'prismjs/components/prism-clike';
import 'prismjs/components/prism-javascript';
import 'prismjs/themes/prism-tomorrow.css';

export default {
    props: {
        code: String | Array | Object,
        enableHiddenInput: {
            default: false,
        },
        maxHeight: {
            type: Number,
            default: 380
        },
        hiddenInputName: {
            default: 'code',
        },
        watchCodeProp: {
            default: false
        },
        editable: {
            type: Boolean,
            default: true
        }
    },
    components: {
        PrismEditor,
    },
    data() {
        let code = this.code;

        if (typeof this.code !== 'string') {
            code = JSON.stringify(code, null, 4);
        }

        return {
            codeString: code.trim() + '\n'
        };
    },
    methods: {
        highlighter(code) {
            return highlight(code, Prism.languages.javascript, languages.js); // returns html
        }
    },
    watch: {
        codeString: function (newVal) {
            this.$emit('update-code', newVal.trim());
        },
        code: function (newVal) {
            if (typeof newVal === 'object' && newVal !== null) {
                newVal = JSON.stringify(newVal);
            }

            if (this.watchCodeProp) {
                this.codeString = (newVal || '').trim() + '\n';
            }
        }
    }
};
</script>

<style>
.my-editor {
    /* we dont use `language-` classes anymore so thats why we need to add background and text color manually */
    background: #f7f7f7;
    color: #343a40;

    /* you must provide font-family font-size line-height. Example: */
    font-family: Fira code, Fira Mono, Consolas, Menlo, Courier, monospace;
    font-size: 14px;
    line-height: 1.5;
    padding: 5px;
}

/* optional class for removing the outline */
/*noinspection CssUnusedSymbol*/
.prism-editor__textarea:focus {
    outline: none;
}
</style>
