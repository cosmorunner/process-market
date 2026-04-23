<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="dataField">Typ</label>
            <select class="form-control" id="dataField" name="data" @change="onTypeChange"
                    :value="column.type_options.type || 'progress_bar'" :disabled="!editable">
                <option value="progress_bar">Fortschritts-Balken</option>
                <option value="icons">Icons</option>
            </select>
        </div>
        <div class="form-group mt-2 mb-0" v-if="(column.type_options.type || 'progress_bar') === 'progress_bar'">
            <label class="mb-0" for="min">Min</label>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control" aria-label="Min" :value="minField ? '' : min" id="min"
                       @input="onMinInput" :readonly="minField || !editable">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" @click="onMinChange('')" v-if="editable">
                        <span class="material-icons">close</span>
                    </button>
                    <button
                        :class="'btn dropdown-toggle btn-outline-' + (minField && usedColumnAliases.map(ele => ele.alias).includes(min) ? 'success' : 'primary')"
                        type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                        {{ minField ? (aliasLabels[min] || min) : '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button type="button" class="dropdown-item" v-for="item in usedColumnAliases"
                                @click="onMinChange(item.alias)">{{ aliasLabels[item.alias] || item.alias }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mb-2" v-else>
            <label class="mb-0 d-block">Icon</label>
            <IconSelection :selected="column.type_options.image || 'circle'"
                           :manual-icons="manualIcons"
                           :enable-favorites="false"
                           :require-selection="true"
                           :editable="editable"
                           @on-select-icon="onIconUpdate"
            />
        </div>
        <div class="form-group mt-2 mb-0">
            <label class="mb-0" for="min">Max</label>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control" aria-label="Max" :value="maxField ? '' : max" id="max"
                       @input="onMaxInput" :readonly="maxField || !editable">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" @click="onMaxChange('')" v-if="editable">
                        <span class="material-icons">close</span>
                    </button>
                    <button
                        :class="'btn dropdown-toggle btn-outline-' + (maxField && usedColumnAliases.map(ele => ele.alias).includes(max) ? 'success' : 'primary')"
                        type="button" data-toggle="dropdown" :disabled="!editable"
                        aria-haspopup="true" aria-expanded="false">
                        {{ maxField ? (aliasLabels[max] || max) : '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button type="button" class="dropdown-item" v-for="item in usedColumnAliases"
                                @click="onMaxChange(item.alias)">{{ aliasLabels[item.alias] || item.alias }}
                        </button>
                    </div>
                </div>
            </div>
            <small class="text-muted" v-if="column.type_options.type === 'icons'">Es werden maximal 25 Icons in der Spalte angezeigt.</small>
        </div>
        <div class="form-group mt-2 mb-0">
            <label class="mb-0" for="color">Fortschritt - Farbe</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" aria-label="Url" :value="color" id="color" :readonly="!editable"
                       @input="onColorInput">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" @click="onColorChange('')" v-if="editable">
                        <span class="material-icons">close</span>
                    </button>
                    <button
                        :class="'btn dropdown-toggle btn-outline-' + (colorField && usedColumnAliases.map(ele => ele.alias).includes(colorField) ? 'success' : 'primary')"
                        type="button" data-toggle="dropdown" :disabled="!editable"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">build</span>
                        {{ colorField ? (aliasLabels[colorField] || colorField) : '#' }}
                    </button>
                    <div class="dropdown-menu scrollable-dropdown">
                        <button type="button" class="dropdown-item" v-for="item in usedColumnAliases"
                                @click="onColorChange(item.alias)">{{ aliasLabels[item.alias] || item.alias }}
                        </button>
                    </div>
                </div>
            </div>
            <small class="text-muted">HEX-Farbcode eingeben (mit "#") oder Datenfeld auswählen.</small>
        </div>
        <hr class="mt-3">
    </div>
</template>

<script>

import {defaultTypeOptions} from '../columnTypes';
import IconSelection from "../partials/IconSelection";

export default {
    components: {IconSelection},
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        supportData: Object,
        editable: Boolean
    },
    data() {
        return {
            defaultTypeOptions: defaultTypeOptions,
            manualIcons: [
                'circle',
                'thumb_up',
                'thumb_down',
                'grade',
                'flag',
                'lightbulb',
                'person',
                'emoji_events',
                'emoji_emotions',
                'visibility',
                'settings'
            ]
        };
    },
    computed: {
        min() {
            return this.column.type_options.min || '';
        },
        max() {
            return this.column.type_options.max || '';
        },
        minField() {
            let field = this.column.type_options.min || '';
            return field !== '' && !Number.isInteger(+field);
        },
        maxField() {
            let field = this.column.type_options.max || '';
            return field !== '' && !Number.isInteger(+field);
        },
        color() {
            let color = this.column.type_options.color || '';
            return color.startsWith('#') ? color : '';
        },
        colorField() {
            let color = this.column.type_options.color || '';
            return color.startsWith('#') ? null : color;
        }
    },
    methods: {
        onIconUpdate(icon) {
            this.$emit('type-options-change', 'image', icon);
        },
        onTypeChange(e) {
            this.$emit('type-options-change', 'type', e.target.value);
        },
        onMinInput(e) {
            if (!Number.isInteger(+e.target.value)) {
                this.$emit('type-options-change', 'min', this.min);
                return;
            }

            this.$emit('type-options-change', 'min', e.target.value);
        },
        onMaxInput(e) {
            if (!Number.isInteger(+e.target.value)) {
                this.$emit('type-options-change', 'max', this.max);
                return;
            }

            this.$emit('type-options-change', 'max', e.target.value);
        },
        onMinChange(val) {
            this.$emit('type-options-change', 'min', val);
        },
        onMaxChange(val) {
            this.$emit('type-options-change', 'max', val);
        },
        onColorInput(e) {
            let value = e.target.value.startsWith('#') ? e.target.value.trim() : '';
            this.$emit('type-options-change', 'color', value);
        },
        onColorChange(value) {
            this.$emit('type-options-change', 'color', value);
        }
    }
};
</script>
