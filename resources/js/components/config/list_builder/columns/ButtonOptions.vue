<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="buttonType">Typ</label>
            <select class="form-control" id="buttonType" @change="onActionChange" :value="action" :disabled="!editable">
                <option value="link">Link</option>
                <option value="mapping">Mapping</option>
                <option value="post_request">REST-API POST-Request</option>
            </select>
        </div>
        <div class="form-group mt-2 mb-0">
            <label class="mb-0">Farbe</label>
            <div class="input-group input-group-sm mb-2">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                    <span :class="'material-icons text-' + color">lens</span>
                </button>
                <div class="dropdown-menu scrollable-dropdown">
                    <button type="button" class="dropdown-item" v-for="selectColor in colorSelection"
                            @click="onColorChange(selectColor)">
                        <span :class="'material-icons mi-2x text-' + selectColor">lens</span>
                    </button>
                </div>
            </div>
        </div>
        <div v-if="action === 'mapping'">
            <div class="form-group mb-2">
                <label class="mb-0" for="label">Button-Label</label>
                <input type="text" class="form-control form-control-sm" aria-label="Button-Label" :readonly="!editable"
                       @input="$emit('type-options-change', 'label', $event.target.value)">
            </div>
        </div>
        <UrlOptions
            v-if="action === 'link'"
            :type-options="typeOptions"
            :alias-labels="aliasLabels" :conditions="conditions"
            :used-column-aliases="usedColumnAliases"
            :default-presets="getDefaultPresets()"
            :binding-value-labels="getUrlBindingValueLabels()"
            :editable="editable"
            v-on="$listeners"
        />
        <PostRequestOptions
            v-if="action === 'post_request'"
            :type-options="typeOptions"
            :alias-labels="aliasLabels"
            :used-column-aliases="usedColumnAliases"
            :default-presets="{}"
            :support-data="supportData"
            :editable="editable"
            :binding-value-labels="getUrlBindingValueLabels()"
            v-on="$listeners"
        />
        <div class="form-group mt-0 mb-0">
            <HideOptions
                :alias-labels="aliasLabels"
                :used-column-aliases="usedColumnAliases"
                :hide="hide"
                :editable="editable"
                v-on="$listeners"/>
        </div>
    </div>
</template>

<script>

import HideOptions from "./partials/HideOptions";
import UrlOptions from "./partials/UrlOptions";
import PostRequestOptions from "./partials/PostRequestOptions";

export default {
    components: {
        PostRequestOptions,
        UrlOptions,
        HideOptions
    },
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        supportData: Object,
        editable: Boolean
    },
    data() {
        return {
            colorSelection: [
                'primary',
                'secondary',
                'success',
                'danger',
                'warning',
                'info'
            ]
        };
    },
    computed: {
        action: function () {
            return this.column.type_options.action || 'link';
        },
        label: function () {
            return this.column.type_options.label || '';
        },
        color: function () {
            return this.column.type_options.color || '';
        },
        hide: function () {
            return this.column.type_options.hide || [];
        },
        typeOptions: function () {
            return this.column.type_options;
        },
        conditions: function () {
            return this.column.type_options.conditions || [];
        }
    },
    methods: {
        onColorChange(color) {
            this.$emit('type-options-change', 'color', color);
        },
        onActionChange(e) {
            this.$emit('type-options-change', 'action', e.target.value);
        },
        getUrlBindingValueLabels() {
            let valueLabels = {};

            this.usedColumnAliases.map(ele => ele.alias).forEach(ele => {
                valueLabels[ele] = this.aliasLabels[ele];
            });

            return valueLabels;
        },
        getDefaultPresets() {
            return {
                process: {
                    url: '',
                    label: '#',
                    bindings: []
                },
                ...this.supportData.systemUrls,
                ...this.supportData.processUrls
            };
        }
    }
};
</script>
