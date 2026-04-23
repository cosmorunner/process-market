<template>
    <div>
        <div class="form-group mb-2">
            <span class="d-block mb-2">
                <span class="material-icons">visibility</span>
                <span>Anzeigen wenn...</span>
            </span>
            <div class="mb-3">
                <ConditionsTable :conditions="[...conditions]" @delete-condition="onDeleteCondition"/>
            </div>
            <ConditionsAdd @add-condition="onConditionAdd"/>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0" for="label">Link-Label</label>
            <input type="text" class="form-control form-control-sm" aria-label="Url" v-model="label" id="label"
                   :readonly="!editable" @change="updateLabel">
            <small class="text-muted" v-if="showLabelHelper">Wird nur genutzt wenn das primäre und sekundäre Datenfeld
                leer sind und keine Konkatenation genutzt wird.</small>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0">Icon</label>
            <IconSelection :selected="typeOptions.image" @on-select-icon="onIconChange" :require-selection="false"
                           :editable="editable"/>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0" for="url">URL</label>
            <div class="dropdown mb-2">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        :disabled="!editable" aria-haspopup="true" aria-expanded="false">{{ presetToLabel() }}
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button v-for="item in sortedDefaultPresets" type="button" class="dropdown-item"
                            @click="updatePreset(item.key)">{{ item.label }}
                    </button>
                </div>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" aria-label="Url" v-model="url" id="url" @change="updateUrl"
                       :readonly="!editable">
            </div>
            <small v-if="!disablePlaceholders" class="text-muted">Füge Platzhalter mit "$" ein.</small>
        </div>
        <div class="row" v-if="placeholderCount > 0">
            <div class="col-6">
                <div class="form-group">
                    <label class="mb-0" for="url">1. Platzhalter</label>
                    <div class="dropdown">
                        <button
                            :class="'btn btn-sm btn-block dropdown-toggle ' + (Object.keys(bindingValueLabels).includes(bindings[0]) ? 'btn-outline-success' : 'btn-outline-danger')"
                            type="button" id="firstPlaceholder" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" :disabled="!editable">
                            {{ bindingValueLabels[bindings[0]] || '?' }}
                        </button>
                        <div class="dropdown-menu dropdown-max-height-250" aria-labelledby="firstPlaceholder">
                            <button type="button" class="dropdown-item" v-for="(label, value) in bindingValueLabels"
                                    @click="updateBinding(0, value)">{{ label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6" v-if="placeholderCount > 1">
                <div class="form-group">
                    <label class="mb-0" for="url">2. Platzhalter</label>
                    <div class="dropdown">
                        <button
                            :class="'btn btn-sm btn-block dropdown-toggle ' + (Object.keys(bindingValueLabels).includes(bindings[1]) ? 'btn-outline-success' : 'btn-outline-danger')"
                            type="button" id="secondPlaceholder" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" :disabled="!editable">
                            {{ bindingValueLabels[bindings[1]] || '?' }}
                        </button>
                        <div class="dropdown-menu dropdown-max-height-250" aria-labelledby="secondPlaceholder">
                            <button type="button" class="dropdown-item" v-for="(label, value) in bindingValueLabels"
                                    @click="updateBinding(1, value)">{{ label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="dataField">Klickverhalten</label>
            <select class="form-control" id="dataField" @change="onClickBehaviorChange" v-model="target"
                    :disabled="!editable">
                <option value="_self">Gleicher Tab</option>
                <option value="_blank">Neuer Tab</option>
            </select>
        </div>
    </div>
</template>

<script>

import IconSelection from "./IconSelection";
import ConditionsAdd from "./ConditionsAdd";
import ConditionsTable from "./ConditionsTable.vue";

export default {
    components: {
        ConditionsTable,
        ConditionsAdd,
        IconSelection
    },
    props: {
        typeOptions: Object,
        bindingValueLabels: Object,
        usedColumnAliases: Array | Object,
        showLabelHelper: {
            default: true
        },
        disablePlaceholders: {
            default: false
        },
        defaultPresets: {
            required: false,
            type: Object,
            default: () => ({
                process: {
                    url: '/processes/$',
                    label: 'Prozess',
                    bindings: ['']
                }
            })
        },
        preset: {
            required: false,
            type: String,
            default: 'process'
        },
        editable: {
            type: Boolean,
            default: true
        },
        conditions: Array
    },
    data() {
        return {
            url: this.typeOptions.url || '',
            label: this.typeOptions.label || '',
            bindings: this.typeOptions.bindings || [],
            target: this.typeOptions.target || '_self',
            maxBindingsSize: 2, // Maximale Anzahl an Ersetzungen in der URL
            presetValue: this.preset
        };
    },
    computed: {
        placeholderCount: function () {
            if (this.disablePlaceholders) {
                return 0;
            }

            return (this.url.match(/\$/g) || []).length;
        },
        sortedDefaultPresets() {
            let items = [];

            for (let key in this.defaultPresets) {
                items.push({
                    ...this.defaultPresets[key],
                    key: key
                });
            }

            return items.sort((a, b) => a.label > b.label ? 1 : -1);
        }
    },
    methods: {
        updatePreset(preset) {
            this.presetValue = preset;
            this.url = this.defaultPresets[preset].url;
            this.bindings = this.defaultPresets[preset].bindings;

            this.$emit('type-options-change', 'bindings', this.bindings);
            this.$emit('type-options-change', 'url', this.url);
        },
        presetToLabel() {
            let preset = Object.values(this.defaultPresets).find(ele => ele.url === this.url);

            if (preset) {
                return (preset.label.length > 80 ? preset.label.substr(0, 80) + '...' : preset.label) || '#';
            }

            return '#';
        },
        updateBinding(index, value) {
            let bindings = [...this.bindings];
            bindings[index] = value || '';

            this.bindings = bindings;
            this.$emit('type-options-change', 'bindings', bindings);
        },
        onIconChange(icon) {
            this.$emit('type-options-change', 'image', icon);
        },
        updateUrl() {
            this.updateBindingsSize();
            this.$emit('type-options-change', 'url', this.url);

            // Preset ermitteln
            if (Object.values(this.defaultPresets).map(ele => ele.url).includes(this.url)) {
                this.presetValue = Object.keys(this.defaultPresets).find(key => this.defaultPresets[key] === this.url);
            }
            else {
                this.presetValue = '#';
            }
        },
        updateLabel(e) {
            this.$emit('type-options-change', 'label', e.target.value.trim());
        },
        updateBindingsSize() {
            let bindings = Array(this.maxBindingsSize).fill('');

            bindings.length = this.placeholderCount > this.maxBindingsSize ? this.maxBindingsSize : this.placeholderCount;

            this.bindings = bindings.map((ele, index) => this.bindings[index] || ele);
            this.$emit('type-options-change', 'bindings', this.bindings);
        },
        onClickBehaviorChange(e) {
            this.target = e.target.value;
            this.$emit('type-options-change', 'target', this.target);
        },
        onConditionAdd(condition) {
            this.$emit('header-conditions-change', [
                ...this.conditions,
                condition
            ]);
        },
        onDeleteCondition(condition) {
            let conditions = [...this.conditions].filter(function (element) {
                return JSON.stringify(element) !== JSON.stringify(condition);
            });
            this.$emit('header-conditions-change', [
                ...conditions
            ]);
        }
    },
    mounted() {
        if (this.url === '') {
            this.url = this.defaultUrls[this.presetValue] || '';
        }
    }
};
</script>
