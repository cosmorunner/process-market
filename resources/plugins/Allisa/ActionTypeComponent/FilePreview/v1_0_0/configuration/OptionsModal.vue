<template>
    <div>
        <div class="container">
            <div class="row d-flex">
                <div class="col">
                    <AutocompleteSelector :items="options.value ? [options.value] : []"
                                          :label="'Datei'"
                                          :icon="'description'"
                                          :action-type="data.actionType"
                                          :outputs-from-actiontype-only="true"
                                          :syntax-include="['process.outputs', 'reference.outputs']"
                                          :pipe-include="['environment_variables']"
                                          :editable="editable"
                                          @items-changed="options.value = $event.length ? $event[0] : ''"
                    />
                    <div class="invalid-feedback d-block"
                         v-for="error in (validationErrors['value'] || [])">{{ error }}
                    </div>
                    <div class="form-group mb-2 mt-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="show_download" @click="options.show_download = !options.show_download" :disabled="!editable"
                                   :checked="options.show_download">
                            <label class="custom-control-label" for="show_download">Download-Button anzeigen.</label>
                        </div>
                    </div>
                    <div class="form-group mb-3 mt-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="show_empty" @click="options.show_empty = !options.show_empty" :disabled="!editable"
                                   :checked="options.show_empty">
                            <label class="custom-control-label" for="show_empty">Komponente bei fehlendem Dokument anzeigen.</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="mb-0">CSS Maximale Höhe (in Pixel)</label>
                        <input type="number" min="0" class="form-control form-control-sm" id="name" :readonly="!editable" v-model="options.css_max_height"
                               aria-describedby="name" placeholder="Keine Einschränkung"/>
                        <small class="d-block text-muted">Wird nur für Bilder genutzt.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AutocompleteSelector from "../../../../../../js/components/utils/AutocompleteSelector.vue";

export default {
    components: {AutocompleteSelector},
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
    computed: {},
    methods: {},
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

