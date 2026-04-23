<template>
    <div>
        <LoadingSeparator :ui="ui" :clear-error="clearError"/>
        <div class="d-flex justify-content-end">
            <Docs class="mr-2 mb-2" article="config-javascript"/>
        </div>
        <div class="row">
            <div class="col">
                <div class="row mb-2">
                    <div class="col">
                        <javascript-editor :javascript-prop="javascriptString"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import LoadingSeparator from "./LoadingSeparator";
import Docs from "../utils/Docs";
import JavascriptEditor from "./JavascriptEditor.vue";

export default {
    components: {
        JavascriptEditor,
        Docs,
        LoadingSeparator
    },
    computed: {
        ...mapGetters([
            'ui',
            'javascript'
        ]),
        javascriptString() {
            // Return javascript as string instead of an object.
            let javascript = this.javascript || '';
            let string = '{\n';

            if (javascript) {
                let methods = javascript.methods || [];
                let lastIndex = methods.length - 1;
                string += '    methods: {\n';

                methods.forEach((method, index) => {
                    string += '        ' + method.name + ': function(' + method.parameters.join(', ') + ') { ';
                    string += method.body;
                    string += (lastIndex === index) ? '}' : '},';
                    string += '\n';
                });
                string += '    }\n';
            }

            string += '}';
            return string;
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils
    }
};
</script>
