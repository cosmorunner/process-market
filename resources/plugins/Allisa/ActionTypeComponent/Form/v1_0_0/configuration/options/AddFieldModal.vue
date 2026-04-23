<template>
    <div>
        <div class="row d-flex">
            <div class="col">
                <DefaultOptions :field="field" :validation-errors="validationErrors"
                                :input-output-names="data.inputOutputNames"
                                @property-change="onPropertyChange"
                                @type-change="onTypeChange"
                                :editable="editable"
                />
                <component :is="optionsComponentName()"
                           :field="field"
                           :input-output-names="data.inputOutputNames"
                           @property-change="onPropertyChange"
                           :definition="data.definition"
                           :editable="editable"
                />
            </div>
        </div>
    </div>
</template>

<script>

import AlertOptions from "./AlertOptions";
import AutocompleteOptions from "./AutocompleteOptions";
import BoxOptions from "./BoxOptions";
import CheckboxOptions from "./CheckboxOptions";
import CurrencyOptions from "./CurrencyOptions.vue";
import DatepickerOptions from "./DatepickerOptions";
import DefaultOptions from "./DefaultOptions";
import FileOptions from "./FileOptions";
import HiddenOptions from "./HiddenOptions";
import IconOptions from "./IconOptions";
import LinkOptions from "./LinkOptions";
import ListmodalOptions from "./ListmodalOptions";
import ParagraphOptions from "./ParagraphOptions";
import RadioOptions from "./RadioOptions";
import SelectOptions from "./SelectOptions";
import TextOptions from "./TextOptions";
import TextareaOptions from "./TextareaOptions";
import TimepickerOptions from "./TimepickerOptions";
import {customFieldOptions, newFieldOptions} from '../utils';

export default {
    components: {
        DefaultOptions,
        AlertOptions,
        AutocompleteOptions,
        CheckboxOptions,
        CurrencyOptions,
        DatepickerOptions,
        FileOptions,
        IconOptions,
        LinkOptions,
        ListmodalOptions,
        ParagraphOptions,
        RadioOptions,
        SelectOptions,
        TextareaOptions,
        TextOptions,
        HiddenOptions,
        TimepickerOptions,
        BoxOptions
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
        let newField = newFieldOptions();
        newField.name = 'new_field_' + (Math.floor(Math.random() * (999 - 111 + 1)) + 111);

        return {
            field: {...newField},
            originalField: {...newField},
        };
    },
    methods: {
        onPropertyChange(property, value) {
            this.field[property] = value;
        },
        onTypeChange(type) {
            if (type === this.originalField.type) {
                this.field = this.originalField;
            }
            else {
                let options = {
                    name: this.field.name,
                    label: this.field.label,
                    default: '',
                    type: type,
                    helper_text: this.field.helper_text,
                    width: this.originalField.width
                };

                // Bei Autocomplete, Radio und Select die Optionen übernehmen.
                if ([
                    'autocomplete',
                    'radio',
                    'select'
                ].includes(type) && [
                    'autocomplete',
                    'radio',
                    'select'
                ].includes(this.originalField.type)) {
                    options.items = this.originalField.items;
                }

                this.field = {...options, ...customFieldOptions(type)};
            }
        },
        optionsComponentName() {
            return this.field.type.charAt(0).toUpperCase() + this.field.type.slice(1) + 'Options';
        }
    },
    watch: {
        field: {
            handler: function () {
                if (this.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        $data: {
            handler: function (data) {
                // Mit "update-parent-data-" werden die Daten an das Eltern-Modal übergeben, wo die Daten abgelegt werden.
                // Wenn dann der Benutzer "Speichern" klickt (onConfirm), werden diese abgelegten Daten an die
                // onConfirm-Methode übergeben.
                this.$emit('update-parent-data', data.field);
            },
            deep: true
        }
    },
    mounted() {
        // Nachdem das Modal initialisiert wurde, müssen wir im Eltern-Modal die Daten ablegen,
        // die gespeichert werden, wenn der Benutzer auf "Speichern" klickt. Diese Daten werden
        // der onConfirm-Methode übergeben.
        this.$emit('update-parent-data', this.field);
    }
};
</script>

