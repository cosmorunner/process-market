<template>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <ModalHeader :title="header" :docs-article="article" :docs-section="section"/>
            <div class="modal-body p-0">
                <div class="row">
                    <div class="col">
                        <CodeEditor :code="'\n\n\n\n\n\n\n\n'" @code-changed="onCodeChange" :max-length="5000"
                                    :max-height="200" :watch-code-prop="true" :editable="ui.editable"
                                    :style="{ minHeight: '200px' }"/>
                    </div>
                </div>
                <div class="row px-3">
                    <div class="col bg-light border-top py-1">
                        <small class="text-muted">Ein Eintrag pro Zeile.</small>
                    </div>
                </div>
                <div class="row px-3 border-top" v-if="ui.errorCode">
                    <div class="col-12 py-1">
                        <div v-for="error in (valueErrors || [])">
                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                        </div>
                        <div v-for="(errorObject, key) in (valueRowsErrors || [])">
                            <template v-for="(errors, property) in errorObject">
                                <div class="invalid-feedback d-block mt-0" v-for="error in errorObject[property]">
                                    <span>{{ (+(key.substring(6)) + 1) }}. Eintrag: </span>
                                    <span>{{ error }}</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="row px-3" v-if="format">
                    <div class="col border-bottom border-top py-1">
                        <span>Format: </span>
                        <code>{{ format }}</code>
                    </div>
                </div>
                <div class="row" v-if="filteredExamples.length">
                    <div class="col">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-3 py-2" v-for="example in filteredExamples">
                                <div class="align-items-center">
                                    <code class="d-inline-block">{{ example.syntax }}</code>
                                    <CopyValueIcon :value="example.syntax"></CopyValueIcon>
                                </div>
                                <span class="text-muted">{{ example.description }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" :on-save="onSave" :save-label="title"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import ActionTypeModal from "./ActionTypeModal.vue";
import ActionType from "./ActionType.vue";
import CodeEditor from "../utils/CodeEditor.vue";
import Docs from "../utils/Docs.vue";
import CopyValueIcon from "../utils/CopyValueIcon.vue";

export default {
    components: {
        Docs,
        CopyValueIcon,
        CodeEditor,
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            data: {
                value_string: '',
                examples: [],
                format: ''
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        ActionType() {
            return ActionType;
        },
        ActionTypeModal() {
            return ActionTypeModal;
        },
        title() {
            return this.ui.modal.data ? 'Erstellen' : 'Speichern';
        },
        header() {
            return this.ui.modal.data ? this.ui.modal.data.title + " erstellen" : this.ui.modal.data.title + " anpassen";
        },
        actionTypeId() {
            return this.ui.modal.data.actionTypeId;
        },
        valueArray() {
            return [...new Set(this.data.value_string.trim().split('\n').map(value => value.trim()).filter(value => value))];
        },
        format() {
            return (this.ui.modal.data.format || '');
        },
        filteredExamples() {
            return (this.ui.modal.data.examples || []).filter(ele => ele.syntax && ele.description);
        },
        valueErrors() {
            return this.ui.validationErrors.value || [];
        },
        valueRowsErrors() {
            return Object.keys(this.ui.validationErrors || {}).filter(key => key.startsWith('value.')).reduce((carry, key) => ({
                ...carry,
                [key]: this.ui.validationErrors[key]
            }), {});
        },
        article() {
            return this.ui.modal.data.article;
        },
        section() {
            return this.ui.modal.data.section;
        },
        requiresDeleteHtml() {
            return this.ui.modal.data.requiresDeleteHtml;
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let method = this.ui.modal.data.method;
            let data = {
                ...this.ui.modal.data.methodData,
                value: this.valueArray,
            };

            this.patchDefinition(method, data, this.requiresDeleteHtml).then(this.closeModal).catch(() => {
            });
        },
        onCodeChange(code) {
            this.data.value_string = code.trim();
        }
    },
    watch: {
        data: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }
};
</script>
