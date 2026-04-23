<template>
    <div>
        <div class="form-group mb-0">
            <label for="title">Prozesse</label>
            <table class="table mb-0">
                <tbody>
                <tr v-for="item in processTypes">
                    <td>
                        <span>{{ item }}</span>
                    </td>
                    <td class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-light text-danger" @click="deleteProcessType(item)" v-if="editable">
                            <span class="material-icons">close</span>
                        </button>
                    </td>
                </tr>
                <tr v-if="!processTypes.length">
                    <td>
                        <span class="text-muted">
                            <i>Noch keine Prozesse zur Lösung hinzugefügt.</i>
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group mb-0" v-if="editable">
            <table class="table mb-0">
                <tbody>
                <tr>
                    <td>
                        <AutocompleteBasic
                            icon="search"
                            :max-items="10"
                            :items="items"
                            :http-endpoint="urls.query_graphs"
                            :exclude-items="processTypes"
                            :http-items-mapping-function="queryGraphsToAutocompleteItemsMapping"
                            @items-changed="onSelectedAutocompleteItem($event)"
                        />
                    </td>
                    <td class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-success" :disabled="!newProcessTypes.length"
                                @click="addProcessType">
                            <span class="material-icons">add</span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <template v-if="editable">
            <hr class="mt-0 mb-5"/>
            <div class="row">
                <div class="col-7">
                    <span class="text-muted">Die Prozesse werden mit der Admin-Demo synchronisiert.</span>
                </div>
                <div class="col-5 d-flex justify-content-end">
                    <SaveButton :error-code="error"
                                :error-message="errorMessage"
                                :saved="saved"
                                :saving="saving"
                                :label="'Speichern & Synchronisieren'"
                                :on-save="saveAndSync"
                    />
                </div>
            </div>
        </template>
        <div v-for="error in (errors.name || [])">
            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
        </div>
    </div>
</template>

<script>

import AutocompleteTags from "./AutocompleteTags";
import FlashMessages from "./FlashMessages";
import AutocompleteBasic from "./AutocompleteBasic";
import SaveButton from "./SaveButton";

export default {
    components: {
        SaveButton,
        AutocompleteBasic,
        AutocompleteTags,
        FlashMessages
    },
    props: {
        hasPublishedVersion: Boolean,
        solutionProp: Object,
        solutionVersionProp: Object,
        processTypesProp: Object | Array,
        locals: Object,
        urls: Object,
        editable: Boolean
    },
    data() {
        return {
            error: null,
            errors: [],
            errorMessage: '',
            saving: false,
            saved: true,
            solution: this.solutionProp,
            solutionVersion: this.solutionVersionProp,
            processTypes: this.processTypesProp.sort(),
            items: [],
            newProcessTypes: []
        };
    },
    methods: {
        queryGraphsToAutocompleteItemsMapping(responseData) {
            return responseData.map(item => ({
                text: item.full_namespace,
                value: item.full_namespace
            }));
        },
        onSelectedAutocompleteItem(items) {
            this.newProcessTypes = items;
        },
        addProcessType() {
            this.processTypes = Array.from(new Set([
                ...this.processTypes,
                ...this.newProcessTypes
            ])).sort();

            this.items = [];
        },
        deleteProcessType(processType) {
            this.processTypes = [...this.processTypes.filter(ele => ele !== processType)];
        },
        saveAndSync() {
            this.patch(this.processTypes);
        },
        patch(processTypes) {
            let that = this;
            let data = {
                ...this.solutionVersion,
                data: {
                    ...this.solutionVersion.data,
                    process_types: processTypes
                }
            };

            this.saving = true;

            return axios.patch(this.urls.update_solution_version, data).then(() => {
                this.saving = false;
                this.saved = true;
            }).catch((error) => {
                that.error = true;
                that.saving = false;
                that.errorMessage = error.response.data.message;
                that.errors = error.response.data.errors || [];
            });
        }
    },
    watch: {
        processTypes() {
            this.errors = [];
            this.errorMessage = null;
            this.saved = false;
        }
    }
};
</script>
