<template>
    <div>
        <div class="row" @click="handleOpenOptionsModal">
            <div class="col">
                    <span v-if="listConfig">
                        <span class="text-muted">
                            <span>Liste: {{ listConfig.name }} - {{ listConfig.slug }}</span>
                        </span>
                        <hr v-if="Object.keys(componentOptions.mapping ||{}).length"/>
                        <span class="text-muted d-block" v-if="Object.keys(componentOptions.mapping ||{}).length">Daten-Mapping: </span>
                        <template v-for="key in (Object.keys(componentOptions.mapping ||{}).sort())">
                            <span class="d-block mb-1">
                                <span>{{ key }}</span>
                                <span>→</span>
                                <span>{{ componentOptions.mapping[key] || '' }}</span>
                            </span>
                        </template>
                        <span class="text-muted d-block" v-if="Object.keys(componentOptions.binding ||{}).length">Query-Parameter Binding: </span>
                        <template v-for="key in (Object.keys(componentOptions.binding ||{}).sort())">
                            <span class="d-block mb-1">
                                <span>{{ key }}</span>
                                <span>←</span>
                                <span>{{ componentOptions.binding[key] || '' }}</span>
                            </span>
                        </template>
                        <div v-if="componentOptions.hide_when_empty || false">
                            <span class="badge badge-light">Versteckt wenn leer</span>
                        </div>
                    </span>
                <span v-else-if="!componentOptions.list_config_id" class="text-danger"><i>Keine Liste ausgewählt.</i></span>
                <span v-else class="text-danger"><i>Liste existiert nicht.</i></span>
            </div>
        </div>
    </div>
</template>

<script>

import OptionsModal from "./OptionsModal";

export default {
    components: {
        OptionsModal
    },
    props: {
        componentId: String,
        componentOptions: Object,
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
    data() {
        return {
            modal: null
        };
    },
    computed: {
        listConfig() {
            return this.definition.list_configs.find(ele => ele.id === this.componentOptions.list_config_id) || null;
        }
    },
    methods: {
        handleOpenOptionsModal() {
            this.openModal({
                component: OptionsModal,
                onConfirm: this.updateOptions,
                data: {
                    listConfigs: this.definition.list_configs,
                    options: this.componentOptions,
                    actionTypeOutputs: this.actionTypeOutputs
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
    },
    mounted() {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }
};
</script>
