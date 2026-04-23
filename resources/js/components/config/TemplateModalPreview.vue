<template>
    <div>
        <div v-if="loading" class="d-flex align-content-center py-2">
            <img src="/img/loading.gif" alt="Loading" width="33" height="33" class="m-auto"/>
        </div>
        <div v-else>
            <TemplateModalPreviewData :template="template" v-if="previewNavigation === 'preview-data'"
                                      @update-preview-data="updatePreviewData" :preview-dataset="selectedDataset"/>
            <KeepAlive>
                <TemplateModalPreviewResult :template="template" v-if="previewNavigation.startsWith('preview-result')"
                                            :preview-dataset="selectedDataset" :output="output"/>
            </KeepAlive>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import CodeEditor from "./CodeEditor";
import OptionBadges from "../utils/OptionBadges";
import AutocompleteSelector from "../utils/AutocompleteSelector.vue";
import TemplateModalPreviewData from "./TemplateModalPreviewData.vue";
import TemplateModalPreviewResult from "./TemplateModalPreviewResult.vue";
import {throttle} from "lodash";

export default {
    components: {
        TemplateModalPreviewResult,
        TemplateModalPreviewData,
        AutocompleteSelector,
        OptionBadges,
        CodeEditor
    },
    props: {
        template: Object,
        previewNavigation: String
    },
    data() {
        return {
            loading: false,
            error: null,
            errorMessage: '',
            previewDatasets: {},
            selectedDataset: {}
        };
    },
    computed: {
        ...mapGetters(['ui']),
        output() {
            if (this.previewNavigation === 'preview-result-html') {
                return 'html';
            }
            if (this.previewNavigation === 'preview-result-pdf') {
                return 'pdf';
            }

            if (this.previewNavigation === 'preview-result-json') {
                return 'json';
            }

            return '';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        updatePreviewData(name, value, removeIfEmpty = false) {
            // Empty global values are set to NULL and not stored in dataset. This indicates that the global field should not be used for rendering and its default value should be used.
            let values = {
                ...this.selectedDataset.values,
                [name]: value
            };

            if (removeIfEmpty && (value === null || value === '' || (typeof value === 'object' && !Object.keys(value).length) || (Array.isArray(value) && !value.length))) {
                delete values[name];
            }

            this.selectedDataset = {
                ...this.selectedDataset,
                values
            };
        },
        updateDataset: throttle(function () {
            let endpoint = this.ui.urls.update_preview_dataset.replace('-templateId-', this.template.id).replace('-datasetId-', this.selectedDataset.id);
            let that = this;

            let params = {values: this.selectedDataset.values};

            // Remove NULL values. Empty global variables are set to null to indicate that they should not be sent.

            axios.patch(endpoint, params).then(function (response) {
                that.previewDatasets = response.data;
                that.loading = false;
            }).catch(function (error) {
                that.loading = false;
                that.error = true;
                that.errorMessage = error.response.data.message;
            });
        }, 1500)
    },
    created() {
        // Only when preview data is empty and there should be entries due to existing mappings.
        if (!Object.keys(this.template.mapping || {}).length && Object.keys(this.selectedDataset).length) {
            return;
        }

        this.loading = true;

        let endpoint = this.ui.urls.get_preview_datasets.replace('-templateId-', this.template.id);
        let that = this;

        axios.get(endpoint).then(function (response) {
            that.previewDatasets = response.data;
            // Here index 0 is used as there could be more datasets in the future, for now there is only one.
            that.selectedDataset = {...that.previewDatasets[0]};
            that.loading = false;
        }).catch(function (error) {
            that.loading = false;
            that.error = true;
            that.errorMessage = error.response.data.message;
        });
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
        selectedDataset: {
            handler() {
                this.updateDataset();
            },
            deep: true
        }
    }

};
</script>
