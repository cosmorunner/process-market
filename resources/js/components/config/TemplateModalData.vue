<template>
    <div class="d-flex flex-column flex-grow-1">
        <div class="alert alert-danger p-2" role="alert" v-if="errorMessage">
            {{ errorMessage }}
        </div>
        <div class="row">
            <div class="col">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon3">Name</span>
                    </div>
                    <input type="text" class="form-control" id="name" name="name" v-model="template.name" maxlength="80" :readonly="!ui.editable"/>
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon3">ID: {{ template.id }}</span>
                    </div>
                </div>
                <div v-for="error in (ui.validationErrors.name || [])">
                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                </div>
            </div>
            <div class="col">
                <div class="form-group input-group-sm mb-0" v-if="template.type === 'html'">
                    <AllowedHtmlTags/>
                </div>
            </div>
        </div>
        <div class="form-group input-group-sm mb-0 flex-grow-1" ref="codeEditorWrapper" v-if="template.hasOwnProperty('data')" :style="'height:' + (ui.modal.bodyHeight - 100) + 'px'">
            <CodeEditor :code="base64decode(template.data)" @update-code="encodeData" :editable="ui.editable" :max-height="ui.modal.bodyHeight - 100" :fill-empty-lines-up-to="50"/>
            <div v-for="error in (ui.validationErrors.name || [])">
                <div class="invalid-feedback d-block mt-0">{{ error }}</div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "./CodeEditor";
import {Base64} from 'js-base64';
import OptionBadges from "../utils/OptionBadges";
import GlobalTemplateData from "./partials/GlobalTemplateData";
import AllowedHtmlTags from "./partials/AllowedHtmlTags.vue";
import AutocompleteSelector from "../utils/AutocompleteSelector";

export default {
    components: {
        AutocompleteSelector,
        GlobalTemplateData,
        AllowedHtmlTags,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object
    },
    data() {
        return {
            loading: false,
            error: null,
            errorMessage: '',
            editMappingKey: null,
            editMappingName: null,
            editMapping: {},
            childElementHeight: 109, // Template Navigation + <hr> + Name Input field
            newMapping: {
                name: '',
                description: '',
                type: 'string',
                value: ''
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
            'list_configs',
            'relation_types_with_single_process',
            'graphs_output_names',
            'graphs_status_types',
            'environments'
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        base64decode: Base64.decode,
        base64encode: Base64.encode,
        encodeData(htmlString) {
            this.$emit('update-property', 'data', this.base64encode(htmlString))
        }
    },
    watch: {
        newMapping: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        template: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    mounted() {
        this.codeEditorWrapperOffsetHeight = this.$refs.codeEditorWrapper.offsetHeight;
    }

};
</script>
