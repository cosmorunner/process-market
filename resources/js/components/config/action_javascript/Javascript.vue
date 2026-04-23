<template>
    <div class="row" v-if="actionType">
        <div class="col-12">
            <div class="row mt-2">
                <div class="col-12 mb-2">
                    <Editor :javascript-prop="javascript" :action-type="actionType"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import Editor from "./Editor.vue";
import Docs from "../../utils/Docs";

export default {
    components: {
        Docs,
        Editor
    },
    props: {
        actionType: Object,
    },
    data() {
        return {};
    },
    computed: {
        javascript() {
            // Return javascript as string instead of an object.
            let javascript = this.actionType.javascript || '';
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
    }
};
</script>
