<template>
    <div>
        <div class="form-group mb-2">
            <label for="display" class="mb-0">Link-Anzeigetext</label>
            <input type="text" class="form-control form-control-sm" :readonly="!editable"
                   id="display" v-model="field.link_label" aria-describedby="link_label"/>
            <small class="text-muted">Leer lassen um die Url als Anzeigetext zu nutzen.</small>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0">Icon</label>
            <IconSelection :selected="field.icon" @on-select-icon="onIconChange" :require-selection="false" :editable="editable"/>
        </div>
        <div class="form-group mb-2">
            <label for="url" class="mb-0">Url</label>
            <input type="text" class="form-control form-control-sm" :readonly="!editable"
                   id="url" v-model="field.url" aria-describedby="link_label"/>
            <small class="text-muted">Leer lassen um den Vorlade-Wert des Feldes zu nutzen.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="style">Style</label>
            <select class="form-control" id="style" v-model="field.style" :disabled="!editable">
                <option value="link">{{ labels('link') }}</option>
                <option value="button">{{ labels('button') }}</option>
                <option value="button-block">{{ labels('button-block') }}</option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="style">Klickverhalten</label>
            <select class="form-control" id="target" v-model="field.target" :disabled="!editable">
                <option value="_self">Gleicher Tab</option>
                <option value="_blank">Neuer Tab</option>
            </select>
        </div>
        <ComputedInputOptions :computed-input="computedInput" v-on="$listeners" class="mb-0" :editable="editable"
                              :helper-text="'Der Rückgabewert muss eine gültige URL sein (http://... oder https://...)'"/>
    </div>
</template>

<script>

import IconSelection from "./partials/IconSelection";
import ComputedInputOptions from "./partials/ComputedInputOptions";

export default {
    components: {
        ComputedInputOptions,
        IconSelection
    },
    props: {
        field: Object,
        inputOutputNames: Array,
        definition: Object,
        editable: Boolean
    },
    computed: {
        computedInput: function () {
            return (this.field.computed_input || '').trim();
        }
    },
    methods: {
        labels(type) {
            return {
                'link': 'Link',
                'button': 'Button',
                'button-block': 'Block-Button',
            }[type] || '?';
        },
        onIconChange(icon){
            this.$emit('property-change', 'icon', icon);
        }
    }
};
</script>
