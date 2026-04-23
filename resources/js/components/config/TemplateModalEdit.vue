<template>
    <div>
        <div class="modal-body px-2 py-0" :style="'height:' + ui.modal.bodyHeight + 'px'">
            <ul class="nav nav-pills my-2">
                <li class="nav-item">
                    <button :class="'nav-link px-3 py-1' + (navigation === 'data' ? ' active' : '')" type="button" @click="navigation = 'data'">Bearbeiten</button>
                </li>
                <li class="nav-item">
                    <button :class="'nav-link px-3 py-1' + (navigation === 'data-mapping' ? ' active' : '')" type="button" @click="navigation = 'data-mapping'">Daten-Mapping</button>
                </li>
                <li class="nav-item">
                    <button :class="'nav-link px-3 py-1' + (navigation === 'preview' ? ' active' : '')" type="button" @click="navigation = 'preview'">Vorschau</button>
                </li>
                <li class="nav-item" v-if="navigation === 'preview'">
                    <span class="nav-link px-3 py-1 disabled" type="button">
                        <span class="material-icons">double_arrow</span>
                    </span>
                </li>
                <li class="nav-item" v-if="navigation === 'preview'">
                </li>
                <li class="nav-item ml-2" v-if="navigation === 'preview'">
                    <template v-if="template.type === 'html'">
                        <div class="btn-group border rounded h-100" role="group" aria-label="Basic example">
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-data' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-data'">Werte
                            </button>
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-result-html' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-result-html'">HTML
                            </button>
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-result-pdf' ? ' active text-white bg-dark' : '')"
                                v-if="template.type === 'html'" type="button"
                                @click="previewNavigation = 'preview-result-pdf'">PDF
                            </button>
                        </div>
                    </template>
                    <template v-if="template.type === 'mustache_list_column'">
                        <div class="btn-group border rounded h-100" role="group" aria-label="Basic example">
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-data' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-data'">Werte
                            </button>
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-result-html' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-result-html'">HTML
                            </button>
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-result-json' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-result-json'">JSON
                            </button>
                        </div>
                    </template>
                    <template v-if="template.type === 'custom_logic'">
                        <div class="btn-group border rounded h-100" role="group" aria-label="Basic example">
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-data' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-data'">Werte
                            </button>
                            <button
                                :class="'btn btn-sm px-3 py-1' + (previewNavigation === 'preview-result-json' ? ' active text-white bg-dark' : '')"
                                type="button" @click="previewNavigation = 'preview-result-json'">JSON
                            </button>
                        </div>
                    </template>
                </li>
            </ul>
            <hr />
            <TemplateModalData :template="template" v-on="$listeners" v-if="navigation === 'data'" />
            <TemplateModalDataMapping :template="template" v-on="$listeners" v-if="navigation === 'data-mapping'" />
            <KeepAlive>
                <div>
                    <TemplateModalPreview :template="template" v-on="$listeners" v-if="navigation === 'preview'" :preview-navigation="previewNavigation" :output="renderOptions"/>
                </div>
            </KeepAlive>
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
import TemplateModalData from "./TemplateModalData.vue";
import TemplateModalPreview from "./TemplateModalPreview.vue";
import TemplateModalDataMapping from "./TemplateModalDataMapping.vue";

export default {
    components: {
        TemplateModalDataMapping,
        TemplateModalPreview,
        TemplateModalPreviewData: () => import("./TemplateModalPreviewData.vue"),
        TemplateModalPreviewResult: () => import("./TemplateModalPreviewResult.vue"),
        TemplateModalData,
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
            navigation: 'data',
            previewNavigation: 'preview-data',
            output: 'html',
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
        renderOptions(){
            if(this.previewNavigation === 'preview-result-html') {

            }
            if(this.previewNavigation === 'preview-result-pdf') {
                return {
                    output: 'pdf'
                }
            }
            if(this.previewNavigation === 'preview-result-json') {

            }
        }
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        base64decode: Base64.decode,
        base64encode: Base64.encode
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
    }

};
</script>
