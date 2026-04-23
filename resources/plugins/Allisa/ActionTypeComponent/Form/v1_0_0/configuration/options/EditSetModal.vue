<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Label</label>
            <input class="form-control" v-model="label" type="text" :readonly="!editable"/>
        </div>
        <div class="form-group mb-2">
            <label for="name" class="mb-0">CSS-Klassen</label>
            <input type="text" class="form-control form-control-sm" id="css_classes" v-model="css_classes" :readonly="!editable"/>
            <small class="text-muted">Mehrere Klassen mit Leerzeichen trennen.</small>
        </div>
        <hr class="my-3"/>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="multiple" @click="toggleEnabled" :disabled="!editable"
                   :checked="multiple.enabled">
            <label class="custom-control-label" for="multiple">Mehrfach-Set</label>
        </div>
        <small class="text-muted">Mit einem "+" und "-" Button dynamisch viele Set-Zeilen hinzufügen/entfernen.</small>
        <div v-if="multiple.enabled">
            <div class="container-fluid p-0 mt-3">
                <div class="row">
                    <div class="col">
                        <div class="alert alert-info p-2 disable-user-select" role="alert">
                            Beachten Sie, dass die Aktions-Daten der Felder im Set vom Typ "JSON-Array" sein müssen (Regeln &
                            Daten > Aktion > Aktions-Daten).
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-block btn-outline-primary dropdown-toggle"
                                    type="button" id="minAnzahl" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" :disabled="!editable">
                                Min. Anzahl: {{ multiple.min }}
                            </button>
                            <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropdownMenuButton">
                                <button type="button" class="dropdown-item"
                                        v-for="index in [...Array(maxListLength)].keys()"
                                        @click="onMinChange(index)">{{ index }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-6">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-block btn-outline-primary dropdown-toggle"
                                    type="button" id="maxAnzahl" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                                Max. Anzahl: {{ multiple.max }}
                            </button>
                            <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropdownMenuButton">
                                <button type="button" class="dropdown-item"
                                        v-for="index in [...Array(maxListLength)].keys()"
                                        @click="onMaxChange(index)">{{ index }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="readonly" :checked="multiple.readonly" :disabled="!editable"
                       @click="toggleReadonly">
                <label class="custom-control-label" for="readonly">Nur lesen</label>
            </div>
            <small class="text-muted">Set-Zeilen können nicht hinzugefügt/entfernt werden.</small>
            <div class="custom-control custom-switch mt-2">
                <input type="checkbox" class="custom-control-input" id="display_empty" :disabled="!editable"
                       :checked="multiple.display_empty_rows" @click="toggleDisplayEmptyRows">
                <label class="custom-control-label" for="display_empty">Nicht befüllte Set-Zeilen anzeigen</label>
            </div>
            <small class="text-muted">Leere Set-Zeilen werden bis zur maximalen Anzahl angezeigt.</small>
            <div class="custom-control custom-switch mt-2">
                <input type="checkbox" class="custom-control-input" id="display_labels_for_each_row" :disabled="!editable"
                       :checked="multiple.display_labels_for_each_row" @click="toggleDisplayLabelsForEachRow">
                <label class="custom-control-label" for="display_labels_for_each_row">Feld-Labels für jede Zeile</label>
            </div>
            <small class="text-muted">Feld-Labels für jede Zeile des Multi-Sets anzeigen.</small>
        </div>
    </div>
</template>

<script>

import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";

export default {
    components: {
        ModalHeader,
        ModalFooter,
    },
    props: {
        data: Object,
        loading: Boolean,
        validationErrors: Object,
        errorCode: Number | null,
        errorMessage: String,
        editable: Boolean
    },
    data() {
        return {
            maxListLength: 51,
            label: this.data.set.label,
            css_classes: this.data.set.css_classes || '',
            multiple: this.data.set.multiple || {
                enabled: false,
                readonly: false,
                display_empty_rows: false,
                display_labels_for_each_row: false,
                min: 0,
                max: 5
            }
        };
    },
    methods: {
        toggleEnabled() {
            this.multiple.enabled = !this.multiple.enabled;
        },
        toggleReadonly() {
            this.multiple.readonly = !this.multiple.readonly;
        },
        toggleDisplayEmptyRows() {
            this.multiple.display_empty_rows = !this.multiple.display_empty_rows;
        },
        toggleDisplayLabelsForEachRow(){
            this.multiple.display_labels_for_each_row = !this.multiple.display_labels_for_each_row;
        },
        onMinChange(val) {
            if (val > this.multiple.max) {
                this.multiple.max = +val;
            }

            this.multiple.min = +val;
        },
        onMaxChange(val) {
            if (val < this.multiple.min) {
                this.multiple.min = +val;
            }

            this.multiple.max = +val;
        },
    },
    watch: {
        $data: {
            handler: function (data) {
                // Daten die beim Öffnen des Modals übergeben wurden.
                let modalData = {...this.data};

                // Mit "update-parent-data-" werden die Daten an das Eltern-Modal übergeben, wo die Daten abgelegt werden.
                // Wenn dann der Benutzer "Speichern" klickt (onConfirm), werden diese abgelegten Daten an die
                // onConfirm-Methode übergeben.
                this.$emit('update-parent-data', {
                    ...modalData.set,
                    label: data.label,
                    css_classes: data.css_classes,
                    multiple: {...data.multiple},
                });
            },
            deep: true
        }
    },
    mounted() {
        // Nachdem das Modal initialisiert wurde, müssen wir im Eltern-Modal die Daten ablegen,
        // die gespeichert werden, wenn der Benutzer auf "Speichern" klickt. Diese Daten werden
        // der onConfirm-Methode übergeben.
        this.$emit('update-parent-data', {
            // Hier setzen wir die Daten auf die "set"-Daten, die wir beim Öffnen des Modals übergeben haben (openModal()).
            ...this.data.set
        });
    }
};
</script>
