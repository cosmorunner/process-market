<template>
    <div>
        <UrlOptions
            :type-options="typeOptions"
            :alias-labels="aliasLabels"
            :used-column-aliases="usedColumnAliases"
            :default-presets="getDefaultPresets()"
            :binding-value-labels="getUrlBindingValueLabels()"
            :conditions="conditions"
            v-on="$listeners"
        />
        <div class="form-group mt-2 mb-0">
            <HideOptions :alias-labels="aliasLabels"
                         :used-column-aliases="usedColumnAliases"
                         :hide="hide"
                         v-on="$listeners"/>
        </div>
        <hr class="mt-3">
    </div>
</template>

<script>

import UrlOptions from "./partials/UrlOptions";
import HideOptions from "./partials/HideOptions";

export default {
    components: {
        HideOptions,
        UrlOptions
    },
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        supportData: Object,
        editable: Boolean
    },
    computed: {
        typeOptions: function () {
            return this.column.type_options;
        },
        hide: function () {
            return this.column.type_options.hide || [];
        },
        conditions: function () {
            return this.column.type_options.conditions || [];
        }
    },
    methods: {
        getUrlBindingValueLabels() {
            let valueLabels = {};

            this.usedColumnAliases.map(ele => ele.alias).forEach(ele => {
                valueLabels[ele] = this.aliasLabels[ele] || ele;
            });

            return valueLabels;
        },
        getDefaultPresets() {
            return {
                process: {
                    url: '/processes/$',
                    label: 'Prozess',
                    bindings: ['']
                },
                ...this.supportData.systemUrls,
                ...this.supportData.processUrls
            };
        }
    }
};
</script>
