<template>
    <div>
        <div class="row" @click="handleOpenOptionsModal">
            <div class="col">
                <div>
                    <ul v-if="isValid" class="list-group list-group-flush">
                        <li class="list-group-item px-2 py-1 bg-transparent">
                            Typ: <span class="text-muted">{{ typeLabel }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent" v-if="componentOptions.label">
                            Label:
                            <span class="text-muted">{{ componentOptions.label }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent" v-if="componentOptions.type === 'icons'">
                            Icon:
                            <span class="material-icons">{{ componentOptions.icon }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent">
                            Wert:
                            <span v-if="Number.isInteger(componentOptions.value)" class="text-muted">{{ componentOptions.value }}</span>
                            <span v-else class="badge badge-light text-muted mr-1" style="font-size: 90%; white-space: normal;"><span class="material-icons">input</span> {{ componentOptions.value }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent">
                            Min:
                            <span v-if="Number.isInteger(componentOptions.min)" class="text-muted">{{ componentOptions.min }}</span>
                            <span v-else class="badge badge-light text-muted mr-1" style="font-size: 90%; white-space: normal;"><span class="material-icons">input</span> {{ componentOptions.min }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent">
                            Max:
                            <span v-if="Number.isInteger(componentOptions.max)" class="text-muted">{{ componentOptions.max }}</span>
                            <span v-else class="badge badge-light text-muted mr-1" style="font-size: 90%; white-space: normal;"><span class="material-icons">input</span> {{ componentOptions.max }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent">
                            Farbe:
                            <span v-if="componentOptions.color.startsWith('#')" class="text-muted">
                                <span class="material-icons" :style="'color:' + componentOptions.color">circle</span>
                            </span>
                            <span v-else class="badge badge-light text-muted mr-1" style="font-size: 90%; white-space: normal;"><span class="material-icons">input</span> {{ componentOptions.color }}</span>
                        </li>
                        <li class="list-group-item px-2 py-1 bg-transparent" v-if="componentOptions.type === 'progress_bar' && componentOptions.show_value">
                            Wert anzeigen:
                            <span class="material-icons">done</span>
                        </li>
                    </ul>
                    <span class="text-danger" v-else><i>Es fehlen Vorlade-Werte.</i></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import OptionsModal from "./OptionsModal";

export default {
    components: {
        OptionsModal,
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
    computed: {
        isValid() {
            return true;
        },
        typeLabel() {
            switch (this.componentOptions.type) {
                case 'progress_bar':
                    return 'Fortschritts-Balken';
                case 'icons':
                    return 'Icons';
            }
        }
    },
    methods: {
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
