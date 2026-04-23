<template>
    <div>
        <div class="container">
            <div class="row d-flex">
                <div class="col">
                    <div class="form-group input-group-sm">
                        <label class="mb-0">Typ</label>
                        <select class="form-control" v-model="options.type">
                            <option value="progress_bar">Fortschritts-Balken</option>
                            <option value="icons">Icons</option>
                        </select>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['type'] || [])">{{ error }}
                        </div>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['type'] || [])">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-2" v-if="options.type === 'icons'">
                        <label class="mb-0">Icon</label>
                        <IconSelection require-selection :selected="options.icon || 'star'" :editable="editable" @on-select-icon="onSelectIcon"/>
                    </div>
                    <div class="form-group mb-2">
                        <label for="label" class="mb-0">Label</label>
                        <input type="text" class="form-control form-control-sm" id="label" v-model="options.label" aria-describedby="label" :readonly="!editable"/>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0">Wert</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" :readonly="!editable"
                                   id="value" v-model="options.value" aria-describedby="value">
                            <div class="input-group-append" v-if="editable">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Vorlade-Daten
                                    <span class="sr-only">Select value</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button class="dropdown-item" type="button" v-for="input in data.actionTypeInputs" @click="options.value = input.name">{{ input.name }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['value'] || [])">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0">Min</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" :readonly="!editable"
                                   id="min" v-model="options.min" aria-describedby="min">
                            <div class="input-group-append" v-if="editable">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Vorlade-Daten
                                    <span class="sr-only">Select min</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button class="dropdown-item" type="button" v-for="input in data.actionTypeInputs" @click="options.min = input.name">{{ input.name }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['min'] || [])">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0">Max</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" :readonly="!editable"
                                   id="max" v-model="options.max" aria-describedby="max">
                            <div class="input-group-append" v-if="editable">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Vorlade-Daten
                                    <span class="sr-only">Select max</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button class="dropdown-item" type="button" v-for="input in data.actionTypeInputs" @click="options.max = input.name">{{ input.name }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['max'] || [])">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0">Farbe</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="color" v-model="options.color" aria-describedby="color" :readonly="!editable">
                            <div class="input-group-append" v-if="editable">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Auswahl
                                    <span class="sr-only">Select color</span>
                                </button>
                                <div class="dropdown-menu scrollable-dropdown">
                                    <button class="dropdown-item" type="button" @click="options.color = '#007bff'">
                                        <span class="material-icons text-primary">circle</span>
                                    </button>
                                    <button class="dropdown-item" type="button" @click="options.color = '#6c757d'">
                                        <span class="material-icons text-secondary">circle</span>
                                    </button>
                                    <button class="dropdown-item" type="button" @click="options.color = '#28a745'">
                                        <span class="material-icons text-success">circle</span>
                                    </button>
                                    <button class="dropdown-item" type="button" @click="options.color = '#17a2b8'">
                                        <span class="material-icons text-info">circle</span>
                                    </button>
                                    <button class="dropdown-item" type="button" @click="options.color = '#ffc107'">
                                        <span class="material-icons text-warning">circle</span>
                                    </button>
                                    <button class="dropdown-item" type="button" @click="options.color = '#dc3545'">
                                        <span class="material-icons text-danger">circle</span>
                                    </button>
                                    <div class="dropdown-divider"></div>
                                    <button class="dropdown-item" type="button" v-for="input in data.actionTypeInputs" @click="options.color = input.name">{{ input.name }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block"
                             v-for="error in (validationErrors['color'] || [])">{{ error }}
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="name" class="mb-0">Hilfe</label>
                        <input type="text" class="form-control form-control-sm" id="name" :readonly="!editable" v-model="options.helper_text"
                               aria-describedby="name"/>
                    </div>
                    <div class="mb-3" v-if="options.type === 'progress_bar'">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1"
                                   :disabled="!editable"
                                   @click="toggleShowValue" :checked="options.show_value">
                            <label class="custom-control-label" for="customSwitch1">Wert anzeigen.</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import IconSelection from "./IconSelection";

export default {
    components: {IconSelection},
    props: {
        data: Object,
        onConfirm: Function,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        validationErrors: Object | Array,
        clearError: Function,
        editable: Boolean
    },
    data() {
        return {
            options: {...this.data.options},
        };
    },
    computed: {
        selectedListConfig() {
            return this.data.listConfigs.find(ele => ele.id === this.options.list_config_id);
        }
    },
    methods: {
        onSelectIcon(icon) {
            this.options.icon = icon;
        },
        toggleShowValue(){
            this.options = {
                ...this.options,
                show_value: !this.options.show_value
            };
        }
    },
    watch: {
        $data: {
            handler: function (data) {
                // Mit "update-parent-data-" werden die Daten an das Eltern-Modal übergeben, wo die Daten abgelegt werden.
                // Wenn dann der Benutzer "Speichern" klickt (onConfirm), werden diese abgelegten Daten an die
                // onConfirm-Methode übergeben.
                this.$emit('update-parent-data', data.options);
            },
            deep: true
        }
    },
    mounted() {
        // Nachdem das Modal initialisiert wurde, müssen wir im Eltern-Modal die Daten ablegen,
        // die gespeichert werden, wenn der Benutzer auf "Speichern" klickt. Diese Daten werden
        // der onConfirm-Methode übergeben.
        this.$emit('update-parent-data', this.data.options);
    }
};
</script>

