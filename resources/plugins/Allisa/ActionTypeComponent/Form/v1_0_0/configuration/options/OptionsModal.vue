<template>
    <div class="container">
        <div class="row d-flex">
            <div class="col">
                <DefaultOptions :field="field"
                                :validation-errors="validationErrors"
                                :input-output-names="sortedInputOutputNames"
                                :editable="editable"
                                @property-change="onPropertyChange"
                                @type-change="onTypeChange"
                />
                <component :is="optionsComponentName()"
                           :field="field"
                           :definition="data.definition"
                           :input-output-names="sortedInputOutputNames"
                           :editable="editable"
                           @property-change="onPropertyChange"
                />
            </div>
        </div>
    </div>
</template>

<script>

import {customFieldOptions, newFieldOptions} from '../utils';
import DefaultOptions from "./DefaultOptions";
import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";
import AlertOptions from "./AlertOptions";
import AutocompleteOptions from "./AutocompleteOptions";
import CheckboxOptions from "./CheckboxOptions";
import CurrencyOptions from "./CurrencyOptions.vue";
import DatepickerOptions from "./DatepickerOptions";
import TimepickerOptions from "./TimepickerOptions";
import FileOptions from "./FileOptions";
import IconOptions from "./IconOptions";
import LinkOptions from "./LinkOptions";
import ListmodalOptions from "./ListmodalOptions";
import ParagraphOptions from "./ParagraphOptions";
import RadioOptions from "./RadioOptions";
import SelectOptions from "./SelectOptions";
import TextareaOptions from "./TextareaOptions";
import TextOptions from "./TextOptions";
import HiddenOptions from "./HiddenOptions";
import BoxOptions from "./BoxOptions";

export default {
    components: {
        ModalHeader,
        DefaultOptions,
        ModalFooter,
        AlertOptions,
        AutocompleteOptions,
        CheckboxOptions,
        CurrencyOptions,
        DatepickerOptions,
        TimepickerOptions,
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
        BoxOptions
    },
    props: {
        data: Object,
        onConfirm: Function,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        validationErrors: Array | Object,
        clearError: Function,
        editable: Boolean
    },
    data() {
        let field = this.data.field;

        if (!field) {
            field = newFieldOptions('new_field_' + (Math.floor(Math.random() * (9991 - 1111 + 1)) + 1111));
        }

        return {
            field: {...field},
            originalField: {...field},
        };
    },
    computed: {
        sortedInputOutputNames() {
            return this.data.inputOutputNames.sort();
        }
    },
    methods: {
        onPropertyChange(property, value) {
            if (typeof value === 'string') {
                value = value.trim();
            }

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
                    width: this.originalField.width,
                    display: this.originalField.display
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
                this.$emit('update-parent-data', {
                    field: data.field,
                    originalName: this.data.originalName
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
            field: this.field,
            originalName: this.data.originalName
        });
    },
};
</script>

