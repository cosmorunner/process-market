<template>
    <div>
        <div class="input-group input-group-sm mb-3">
            <div class="input-group-prepend">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" :disabled="!editable"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">{{ iconForType(field.type) }}</span> {{ typeLabel(field.type) }}
                </button>
                <div class="dropdown-menu">
                    <button type="button" v-for="(value, propertyName) in types" @click="onTypeChange(propertyName)"
                            class="dropdown-item">
                        <span class="material-icons">{{ iconForType(propertyName) }}</span> {{ value }}
                    </button>
                </div>
            </div>
            <input type="text" class="form-control" v-model="field.name" :readonly="!editable">
            <div v-if="inputOutputNames.length && editable" class="input-group-append">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <span>Datenfelder</span>
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button type="button" v-for="name in inputOutputNames" @click="field.name = name"
                            class="dropdown-item">
                        {{ name }}
                    </button>
                </div>
            </div>
        </div>
        <div class="invalid-feedback d-block" v-for="error in errorsFor('name')">{{ error }}</div>
        <div v-if="field.type !== 'hidden' && field.type !== 'box'">
            <div class="form-group mb-2">
                <label for="name" class="mb-0">Label</label>
                <input type="text" class="form-control form-control-sm" id="name" v-model="field.label"
                       aria-describedby="name" :readonly="!editable"/>
            </div>
            <div class="form-group mb-2">
                <label for="name" class="mb-0">Hilfe</label>
                <input type="text" class="form-control form-control-sm" id="helper_text" v-model="field.helper_text"
                       aria-describedby="helper_text" :readonly="!editable"/>
            </div>
            <div class="form-group mb-2">
                <label for="name" class="mb-0">CSS-Klassen</label>
                <input type="text" class="form-control form-control-sm" id="css_classes" v-model="field.css_classes"
                       :readonly="!editable"/>
                <small class="text-muted">Mehrere Klassen mit Leerzeichen trennen.</small>
            </div>
            <div class="form-group mb-2">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="highlightField"
                           @click="toggleHighlightedField" :checked="highlightedField" :disabled="!editable">
                    <label class="custom-control-label" for="highlightField">Feld hervorheben</label>
                </div>
                <small class="text-muted">
                    Das Feld erhält im unbefüllten Zustand eine rote Umrandung um darauf aufmerksam zu machen, dass
                    dieses Feld
                    von besonderer, inhaltlicher Bedeutung ist.
                    Ist unabhängig von der "Pflichtfeld"-Angabe des Datenfeldes.
                </small>
            </div>
        </div>
        <div class="invalid-feedback d-block" v-for="error in errorsFor('label')">{{ error }}</div>
        <div class="invalid-feedback d-block" v-for="error in errorsFor('helper_text')">{{ error }}</div>
    </div>
</template>

<script>

import {iconForType} from '../utils';

export default {
    props: {
        field: Object,
        inputOutputNames: Array,
        validationErrors: Object | Array,
        editable: Boolean
    },
    data() {
        return {
            types: {
                alert: 'Hinweis',
                autocomplete: 'Autocomplete',
                checkbox: 'Checkbox',
                currency: 'Währung',
                datepicker: 'Datepicker',
                file: 'Datei-Upload',
                hidden: 'Verstecktes Feld',
                icon: 'Icon',
                link: 'Link',
                listmodal: 'Dialog-Liste',
                paragraph: 'Paragraph',
                radio: 'Radio',
                select: 'Auswahl',
                text: 'Text',
                textarea: 'Textarea',
                timepicker: 'Zeit-Auswahl',
                box: 'Box'
            }
        };
    },
    computed: {
        highlightedField() {
            return this.field.highlighted || false;
        }
    },
    methods: {
        iconForType: iconForType,
        typeLabel(type) {
            return this.types[type] || type;
        },
        onTypeChange(type) {
            this.$emit('type-change', type);
        },
        errorsFor(fieldAttribute) {
            let errors = [], attribute;

            for (attribute in this.validationErrors) {
                if (attribute.startsWith('sets.') && attribute.endsWith(fieldAttribute)) {
                    errors = errors.concat([...this.validationErrors[attribute]]);
                }
            }

            return [...new Set(errors)];
        },
        toggleHighlightedField(e) {
            this.$emit('property-change', 'highlighted', e.target.checked);
        }
    }
};
</script>

