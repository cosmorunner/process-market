<template>
    <div class="form-group m-2">
        <div class="row mb-1" v-for="(value, name) in processData">
            <div class="col-4">
                {{ name }}
            </div>
            <div class="col-7">
                <div v-if="outputByName(name).type === 'basic'">
                    <OptionBadges :value="value"/>
                </div>
                <div v-else>
                    <CodeEditor :code="JSON.stringify(value, null, 4)"
                                @update-code="onCodeChange"
                                :max-length="1500" :watch-code-prop="true" :editable="false"/>
                </div>
            </div>
            <div class="col-1">
                <button class="btn btn-sm btn-light float-right" @click="$emit('delete-process-data-option', name)"
                        v-if="editable">
                    <span class="material-icons text-danger">close</span>
                </button>
            </div>
        </div>
        <span class="text-muted" v-if="!outputs.length">Der gewählte Prozess hat keine Prozess-Datenfelder.</span>
        <div v-if="newOption && unusedOutputs.length && editable" class="mb-3">
            <div class="row mt-2">
                <div class="col-4">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        {{ newOption.name }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button v-for="output in unusedOutputs" type="button" class="dropdown-item"
                                @click="switchOutput(output.name)">{{ output.name }}
                        </button>
                    </div>
                </div>
                <div v-if="newOption.type === 'basic'" class="col-7">
                    <AutocompleteSelector :items="newOption.value ? [newOption.value] : []" :max-items="1"
                                          :add-only-from-autocomplete="false" :syntax-include="['auth', 'date']"
                                          :pipe-include="['environment_variables', 'environment_users', 'environment_groups']"
                                          @items-changed="$event.length ? newOption.value = $event[0] : newOption.value = ''"/>
                </div>
                <div v-else class="col-7" id="add-process-data-modal-process-data-editor">
                    <div>
                        <small class="text-muted">
                            <span>JSON-Eingabe - </span>
                            <span class="material-icons">info</span>
                            <span>Alle Zeichenketten-Werte werden getrimmt.</span>
                        </small>
                        <CodeEditor :code="JSON.stringify(newOption.value, null, 4)"
                                    @update-code="onCodeChange"
                                    :max-length="1500" :watch-code-prop="true" :editable="editable"/>
                        <small v-if="invalidCode" class="text-danger">Ungültiges JSON</small>
                        <small v-else class="text-success">Gültiges JSON</small>
                    </div>
                    <div class="mt-1">
                        <DropdownSelector :syntax-include="['auth', 'date']"
                                          :pipe-include="['environment_variables', 'environment_users', 'environment_groups']"
                                          @selected="copyText"
                                          :dropdown-width="766" v-if="editable"/>
                        <span class="text-success" v-if="showCopied">Kopiert!</span>
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn btn-sm btn-outline-success float-right" @click="addProcessDataOption">
                        <span class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import utils from '../../../develop-utils';
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import OptionBadges from "../../utils/OptionBadges";
import CodeEditor from "../CodeEditor.vue";
import DropdownSelector from "../../utils/DropdownSelector.vue";

export default {
    components: {
        DropdownSelector,
        CodeEditor,
        OptionBadges,
        AutocompleteSelector,
    },
    props: {
        outputs: Array,
        processDataProp: Object | Array,
        editable: Boolean,
    },
    data() {
        return {
            newOption: null,
            showCopied: false,
            invalidCode: false
        };
    },
    computed: {
        unusedOutputs() {
            if (this.processData === null) {
                return this.outputs;
            }

            return this.outputs.filter(ele => !Object.keys(this.processData).includes(ele.name));
        },
        processData() {
            return this.processDataProp;
        }
    },
    methods: {
        ...utils,
        switchOutput(name) {
            this.resetNewOption(this.unusedOutputs.find(ele => ele.name === name));
        },
        resetNewOption(output = null) {
            output = output || this.unusedOutputs[0] || null;

            if (output === null) {
                this.newOption = null;
                return null;
            }

            this.newOption = {
                name: output.name,
                value: output.type === 'basic' ? '' : [],
                type: output.type
            };
        },
        addProcessDataOption() {
            this.$emit('add-process-data-option', this.newOption);
        },
        outputByName(name) {
            return this.outputs.find(ele => ele.name === name);
        },
        copyText(item) {
            let that = this;

            this.copy(item.value_with_label).then(function () {
                that.showCopied = true;
            }).catch(() => console.log('error copying'));

            setTimeout(() => {
                this.showCopied = false;
            }, 1500);
        },
        copy(textToCopy) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(textToCopy);
            }
            else {
                // workaround to copy to clipboard
                let textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.getElementById('add-process-data-modal-process-data-editor').appendChild(textArea);
                textArea.focus();
                textArea.select();

                return new Promise((res, rej) => {
                    document.execCommand("copy") ? res() : rej();
                    textArea.remove();
                });
            }
        },
        onCodeChange(code) {
            this.code = code.trim();
            this.invalidCode = false;
            let obj = false;

            try {
                obj = JSON.parse(this.code);
            } catch (e) {
                this.invalidCode = true;
                return;
            }

            this.newOption = {
                ...this.newOption,
                value: obj
            };
        },
    },
    watch: {
        processData() {
            this.resetNewOption();
        },
        outputs() {
            this.resetNewOption();
            this.$emit('clear-process-data-option');
        }
    },
    mounted() {
        this.resetNewOption();
    }
};
</script>
