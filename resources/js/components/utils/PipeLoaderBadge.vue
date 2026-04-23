<template>
    <BaseBadge :label="label" :disable-tooltip="disableTooltip" :tooltip="tooltip" :icon="modelIcon"/>
</template>

<script>

import utils from "../../config-utils";
import BaseBadge from "./BaseBadge";

export default {
    name: 'PipeLoaderBadge',
    components: {BaseBadge},
    props: {
        disableTooltip: {
            default: false,
            type: Boolean
        },
        value: String,
        label: String
    },
    computed: {
        modelName() {
            let value = this.value;

            // z.B. app::connector|7e49a44c-9602-4c24-b3f5-cc91ced16729
            // z.B. allisa/demo@1.0.0::relationType|7e49a44c-9602-4c24-b3f5-cc91ced16729
            if (value.includes('::')) {
                value = value.split('::')[1]
            }

            return value.split('|')[0] || '';
        },
        modelIcon() {
            return this.modelIcons(this.modelName);
        },
        tooltip() {
            return this.disableTooltip ? '' : '';
        }
    },
    methods: {
        ...utils
    }
};
</script>
