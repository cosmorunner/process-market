<template>
    <div>
        <div class="form-group mb-2">
            <label class="mb-0" for="label">Link-Label</label>
            <input type="text" class="form-control form-control-sm" aria-label="Url" v-model="label" id="label" :readonly="!editable"
                   @input="updateLabel">
            <small class="text-muted" v-if="showLabelHelper">Wird nur genutzt wenn das primäre und sekundäre Datenfeld
                leer sind und keine Konkatenation genutzt wird.</small>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0" for="label">Tooltip</label>
            <input type="text" class="form-control form-control-sm" aria-label="Url" v-model="tooltip" id="tooltip"
                   @input="updateTooltip" :readonly="!editable">
            <small class="text-muted">Wird überhalb des Buttons angezeigt.</small>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0">Icon</label>
            <IconSelection :selected="typeOptions.image" @on-select-icon="onIconChange" :require-selection="false" :editable="editable"/>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0" for="url">URL</label>
            <div class="dropdown mb-2">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" :disabled="!editable">{{ presetToLabel() }}
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button v-for="(item, presetName) in defaultPresets" type="button" class="dropdown-item"
                            @click="updatePreset(presetName)">{{ item.label }}
                    </button>
                </div>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" aria-label="Url" v-model="url" id="url" @input="updateUrl" :readonly="!editable">
            </div>
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
        <PostParamsOptions :items="typeOptions.params || []" :binding-value-labels="bindingValueLabels" @items-change="onParamsChange" :editable="editable"/>
    </div>
</template>
<script>

import IconSelection from "./IconSelection";
import PostParamsOptions from "./PostParamsOptions";

export default {
    components: {
        PostParamsOptions,
        IconSelection
    },
    props: {
        supportData: Object,
        typeOptions: Object,
        bindingValueLabels: Object,
        usedColumnAliases: Array | Object,
        showLabelHelper: {
            default: true
        },
        disablePlaceholders: {
            default: false
        },
        preset: {
            required: false,
            type: String,
            default: '#'
        },
        editable: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            url: this.typeOptions.url || '',
            label: this.typeOptions.label || '',
            tooltip: this.typeOptions.tooltip || '',
            bindings: this.typeOptions.bindings || [],
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
        defaultPresets() {
            let items = [];

            for (let urlData in this.supportData.processUrls) {
                if (this.supportData.processUrls[urlData].url.includes('api')) {
                    items.push(this.supportData.processUrls[urlData]);
                }
            }

            return items.reduce(function (carry, item, index) {
                carry[index] = {
                    url: item.url,
                    label: item.label,
                    bindings: item.bindings
                };

                return carry;
            }, {});
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
        onParamsChange(params) {
            this.$emit('type-options-change', 'params', params);
        },
        onIconChange(icon) {
            this.$emit('type-options-change', 'image', icon);
        },
        updateLabel(e) {
            this.$emit('type-options-change', 'label', e.target.value.trim());
        },
        updateTooltip(e) {
            this.$emit('type-options-change', 'tooltip', e.target.value.trim());
        },
        updateBindingsSize() {
            let bindings = Array(this.maxBindingsSize).fill('');

            bindings.length = this.placeholderCount > this.maxBindingsSize ? this.maxBindingsSize : this.placeholderCount;

            this.bindings = bindings.map((ele, index) => this.bindings[index] || ele);
            this.$emit('type-options-change', 'bindings', this.bindings);
        },
    },
    mounted() {
        if (this.url === '') {
            this.url = this.defaultUrls[this.presetValue] || '';
        }
    }
};
</script>
