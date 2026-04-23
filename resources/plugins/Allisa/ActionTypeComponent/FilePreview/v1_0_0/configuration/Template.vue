<template>
    <div>
        <div class="row" @click="handleOpenOptionsModal">
            <div class="col">
                <div>
                    <template v-if="isValid">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-2 py-1 bg-transparent">
                                Datei: <BaseBadge :icon="fileIcon" :label="getSyntaxParts(componentOptions.value).label"/>

                            </li>
                            <li class="list-group-item px-2 py-1 bg-transparent">
                                Download-Button:
                                <span class="material-icons text-success" v-if="componentOptions.show_download">check</span>
                                <span class="material-icons text-dark" v-else>close</span>
                            </li>
                            <li class="list-group-item px-2 py-1 bg-transparent">
                                Leere Komponente anzeigen:
                                <span class="material-icons text-success" v-if="componentOptions.show_empty">check</span>
                                <span class="material-icons text-dark" v-else>close</span>
                            </li>
                            <li class="list-group-item px-2 py-1 bg-transparent" v-if="componentOptions.css_max_height">
                                CSS Maximale Höhe:
                                <span>{{componentOptions.css_max_height}}px</span>
                            </li>
                        </ul>
                    </template>
                    <span v-else class="text-danger">
                        <i>Kein Dokument gewählt.</i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import OptionsModal from "./OptionsModal";
import BaseBadge from "./BaseBadge.vue";
import utils from "./utils";

export default {
    components: {
        BaseBadge,
        OptionsModal,
    },
    props: {
        componentId: String,
        componentOptions: Object|Array,
        changeComponentWidth: Function,
        updateComponentOptions: Function,
        deleteComponent: Function,
        widthLabel: Function,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        clearError: Function,
        setError: Function,
        openModal: Function,
        closeModal: Function,
        validationErrors: Object | Array,
        actionTypeInputs: Array,
        actionTypeOutputs: Array,
        definition: Object,
        modalComponent: Object,
        editable: Boolean
    },
    computed: {
        isValid() {
            return this.componentOptions.value;
        },
        fileIcon() {
            return this.componentOptions.value.startsWith('[[process') ? 'grain' : 'tag'
        },
    },
    methods: {
        ...utils,
        handleOpenOptionsModal() {
            this.openModal({
                component: OptionsModal,
                onConfirm: this.updateOptions,
                data: {
                    options: this.componentOptions,
                    actionTypeInputs: this.actionTypeInputs
                }
            });
        },
        updateOptions(newOptions) {
            let payload = {
                ...this.componentOptions,
                ...newOptions
            };

            return this.updateComponentOptions(this.componentId, payload).then(this.closeModal);
        },
    }
};
</script>
