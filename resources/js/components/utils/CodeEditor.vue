<template>
    <div>
        <prism-editor class="my-editor" :style="{ 'max-height': maxHeight + 'px' }" v-model="source" :highlight="highlighter" line-numbers :ignore-tab-key="false" :readonly="!editable"></prism-editor>
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
        code: {
            required: true,
            type: String
        },
        maxHeight: {
            type: Number,
            default: 430
        },
        editable: Boolean
    },
    components: {
        PrismEditor,
    },
    data(){
        return {
            codeString: this.code
        }
    },
    computed: {
        source: {
            // getter
            get: function () {
                return this.codeString
            },
            // setter
            set: function (newValue) {
                this.codeString = newValue
                this.$emit('code-changed', newValue)
            }
        }
    },
    methods: {
        highlighter(code) {
            return highlight(code, Prism.languages.javascript, languages.js); //returns html
        },
    },
    mounted(){
        this.source = this.code
    }
};
</script>

<style>
/* required class */
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
.prism-editor__textarea:focus {
    outline: none;
}
</style>
