<template>
    <div class="bg-light">
        <div class="container-fluid">
            <div :style="'overflow-y:scroll;height:' + (ui.modal.bodyHeight - 60) + 'px'" class="preview-wrapper">
                <div v-if="loading" class="d-flex align-content-center py-2">
                    <img src="/img/loading.gif" alt="Loading" width="33" height="33" class="m-auto"/>
                </div>
                <div v-else style="height: inherit;">
                    <!-- Render error -->
                    <div v-if="error">
                        <div class="alert alert-warning mb-0" role="alert">
                            {{ errorMessage }}
                        </div>
                    </div>
                    <!-- JSON response -->
                    <div v-if="resultContentType === 'application/json'">
                        <CodeEditor :code="JSON.stringify(result, null, 4)" :editable="false"
                                    :max-height="ui.modal.bodyHeight - 65"/>
                        <button class="btn btn-sm btn-link text-primary" @click="renderResult">
                            <span class="material-icons">autorenew</span>
                        </button>
                    </div>
                    <!-- HTML response -->
                    <div v-if="resultContentType === 'text/html; charset=UTF-8'"
                         :style="'height:' + (ui.modal.bodyHeight - 65) + 'px'">
                        <iframe :srcdoc="result" class="w-100 border h-100"></iframe>
                    </div>
                    <!-- Mustache HTML Result -->
                    <div v-if="resultContentType === 'mustache_html'"
                         :style="'height:' + (ui.modal.bodyHeight - 65) + 'px'">
                        <div class="container mt-4" v-if="output === 'html'">
                            <div class="row justify-content-center">
                                <div class="col-7 border-left border-right bg-white px-0">
                                    <table class="table border-top-0 border-bottom mb-0 text-break">
                                        <thead>
                                        <tr class="d-flex">
                                            <th scope="col" aria-label="Text"
                                                class="d-flex border-bottom-0 px-3 py-2 col-12">
                                                <span
                                                    class="cursor-pointer overflow-hidden whitespace-no-wrap text-overflow-ellipsis">Beispiel-Listenspalte</span>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody style="">
                                        <tr class="d-flex">
                                            <td class="px-3 py-2 col-12">
                                                <div v-html="result.html"></div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div v-if="output === 'json'">
                            <CodeEditor :code="JSON.stringify(result.json, null, 4)" :editable="false"
                                        :max-height="ui.modal.bodyHeight - 65"/>
                            <p class="my-2">
                                <span class="material-icons text-primary">info</span>
                                <span>Mit diesen Daten wurde die Mustache JS Listenspalten-Vorlage gerendert. Die Daten basieren auf dem JSON-Objekt-Rückgabewert beim "Daten-Mapping".</span>
                            </p>
                        </div>

                    </div>
                    <!-- PDF response - Content type is text/plain as result is url to pdf. -->
                    <div v-if="resultContentType === 'text/plain; charset=UTF-8'"
                         :style="'height:' + (ui.modal.bodyHeight - 65) + 'px'">
                        <div class="card">
                            <div class="card-body pb-2">
                                <template v-if="pdf.isLoading">Laden...</template>
                                <template v-else>
                                    <div class="row d-flex justify-content-between">
                                        <div class="col-4">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-primary"
                                                            :disabled="pdf.page <= 1" @click="pdf.page--">
                                                        <span class="material-icons">keyboard_arrow_left</span>
                                                    </button>
                                                </div>
                                                <input type="text" class="form-control" aria-label="Page"
                                                       :value="pdf.page" @change="onInputPage">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">/ {{ pdf.pageCount }}</span>
                                                    <button type="button" class="btn btn-outline-primary"
                                                            :disabled="pdf.page >= pdf.pageCount" @click="pdf.page++">
                                                        <span class="material-icons">keyboard_arrow_right</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="d-flex justify-content-end">
                                                <div>
                                                    <button class="btn btn-sm btn-light"
                                                            @click="$refs.pdfRef.download()">
                                                        Download
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <hr/>
                                <VuePdfEmbed :height="(ui.modal.bodyHeight - 150)" ref="pdfRef" :source="result"
                                             :page="pdf.page" @rendered="handlePdfRender" text-layer/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed';
import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "./CodeEditor";
import {Base64} from 'js-base64';
import OptionBadges from "../utils/OptionBadges";
import AutocompleteSelector from "../utils/AutocompleteSelector.vue";
import Mustache from 'mustache';

export default {
    components: {
        VuePdfEmbed,
        AutocompleteSelector,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object,
        output: String,
        previewDataset: Object | Array
    },
    data() {
        return {
            loading: false,
            error: null,
            errorMessage: '',
            result: null,
            resultContentType: null,
            pdf: {
                show: false,
                source: '',
                isLoading: true,
                page: 1,
                pageCount: 1,
                pageLabel: ''
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        base64encode: Base64.encode,
        setResult(response) {
            this.resultContentType = response.headers['content-type'];

            // base64 encoded pdf
            if (this.resultContentType === 'text/plain; charset=UTF-8') {
                this.result = 'data:application/pdf;base64,' + response.data;
            }
            else {
                this.result = response.data;
            }
        },
        onInputPage(e) {
            let page = +e.target.value;

            if (isNaN(page)) {
                // In case the page is already 1, we first switch to 0 and then to 1.
                this.pdf.page = 0;
                this.pdf.page = 1;
            }
            else {
                this.pdf.page = Math.abs(page) > this.pdf.pageCount ? this.pdf.pageCount : Math.abs(page);
            }
        },
        handlePdfRender() {
            this.pdf.isLoading = false;
            this.pdf.pageCount = this.$refs.pdfRef.pageCount;
            this.pdf.pageLabel = this.pdf.pageCount > 1 ? 'Seiten' : 'Seite';
        },
        renderResult() {
            if (this.template.type === 'html' || this.template.type === 'custom_logic') {
                this.fetchApiResult();
            }
            if (this.template.type === 'mustache_list_column') {
                this.renderMustacheListColumn();
            }

        },
        fetchApiResult() {
            if (!this.template.data) {
                this.error = true;
                this.errorMessage = 'Leeres Template.';
                return;
            }

            this.loading = true;
            this.error = false;
            this.errorMessage = '';

            let that = this;
            let endpoint = this.ui.urls.preview_template.replace('-templateId-', this.template.id);
            let params = {
                template: this.template.data,
                data: this.previewDataset.values,
                options: {
                    output: this.output
                }
            };

            axios.post(endpoint, params).then(function (response) {
                that.loading = false;
                that.setResult(response);
            }).catch(function (error) {
                that.loading = false;
                that.error = true;
                that.errorMessage = error.response.data.message;
            });
        },
        renderMustacheListColumn() {
            this.loading = true;
            let dataMappingJs = this.template.mapping?.js.value || '';

            try {
                let functionObject = Function('row', dataMappingJs);
                let object = functionObject({...this.previewDataset.values.process_list});

                if (typeof object !== 'object' || object === null || Array.isArray(object)) {
                    this.loading = false;
                    this.error = true;
                    this.errorMessage = 'Das JavaScript unter "Daten-Mapping" muss ein JSON-Objeckt zurückgeben.';

                    return;
                }

                let html = Mustache.render(Base64.atob(this.template.data), object).trim();

                this.resultContentType = 'mustache_html';
                this.result = {
                    json: object,
                    html: html
                };
                this.loading = false;
            } catch (e) {
                this.loading = false;
                this.error = true;
                this.errorMessage = e.message;
            }
        }
    },
    watch: {
        template: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        output() {
            this.renderResult();
        },
    },
    activated() {
        this.renderResult();
    },

};
</script>

<style>

.vue-pdf-embed .vue-pdf-embed__page {
    display: flex;
    justify-content: center !important;
}

.vue-pdf-embed .vue-pdf-embed__page canvas {
    border: 1px solid #dee2e6 !important;
}


</style>
