<template>
    <div class="rounded-0 card h-100">
        <div class="card-body p-2">
            <div class="form-group input-group-sm mb-2">
                <CodeEditor :code="javascript" @update-code="updateJavascript" :editable="ui.editable" :max-height="680"
                            :watch-code-prop="true"/>
                <div>
                    <small v-if="invalidJavascript" class="text-danger">Ungültiges JavaScript</small>
                    <small v-else class="text-success">Gültiges JavaScript</small>
                </div>
                <div v-for="error in (ui.validationErrors.javascript || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
        </div>
        <ModalFooter :ui="ui" @save="save" :hide-cancel-button="true" :save-disabled="invalidJavascript"
                     :save-label="'Speichern'"/>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";
import utils from "../../config-utils";
import ModalFooter from "./ModalFooter";
import CodeEditor from "./CodeEditor.vue";

export default {
    components: {
        CodeEditor,
        ModalFooter
    },
    props: {
        javascriptProp: String
    },
    data() {
        return {
            javascript: this.javascriptProp,
            invalidJavascript: false
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ])
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        updateJavascript(code) {
            this.javascript = code;

            this.invalidJavascript = false;
            try {
                eval('(' + code + ')');
            } catch (error) {
                this.invalidJavascript = true;
            }
        },
        getJavascriptMethodBody(string) {
            return string.substring(string.indexOf('{') + 1, string.lastIndexOf('}'));
        },
        getJavascriptMethodParameters(string) {
            let parameters = string.substring(string.indexOf('(') + 1, string.indexOf(')'));
            if (parameters.trim() === '') {
                return [];
            }
            return (parameters.split(',')).map(ele => ele.trim());
        },
        save() {
            let sentJavascript = {};
            try {
                let parsedObject = eval('(' + this.javascript + ')');
                let that = this;

                if (Object.hasOwn(parsedObject, 'methods')) {
                    sentJavascript.methods = [];

                    Object.keys(parsedObject.methods).forEach(function (key) {
                        if (typeof parsedObject.methods[key] === 'function') {
                            let method = {};
                            let methodString = parsedObject.methods[key].toString();

                            method.name = key;
                            method.parameters = that.getJavascriptMethodParameters(methodString);
                            method.body = that.getJavascriptMethodBody(methodString);

                            sentJavascript.methods.push(method);
                        }
                    });
                }
            } catch (error) {
                console.log(error);
                this.invalidJavascript = true;
                return;
            }

            this.patchDefinition('UpdateProcessJavascript', {
                javascript: JSON.stringify(sentJavascript)
            });
        }
    }
};
</script>
