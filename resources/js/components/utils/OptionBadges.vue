<template>
    <span>
        <template v-for="value in items" v-if="!manualLabel || !manualIcon">
            <span v-if="value === ''" :class="(items.length > 1 ? 'mr-1' : '')"><i>Leere Zeichenkette</i></span>
            <span v-else :class="items.length > 1 ? 'mr-1' : ''">
                <component :is="getComponent(value)" :disable-tooltip="disableTooltip" :value="cleanValue(value)"
                           :label="getLabel(getComponent(value), value)" :raw-value="value"/>
            </span>
        </template>
        <template v-if="manualLabel && manualIcon">
            <BaseBadge :label="manualLabel" :disable-tooltip="disableTooltip" :tooltip="manualTooltip"
                       :icon="manualIcon"/>
        </template>
    </span>
</template>

<script>

import PipeLoaderBadge from "./PipeLoaderBadge";
import NoBadge from "./NoBadge";
import SyntaxLoaderBadge from "./SyntaxLoaderBadge";
import BaseBadge from "./BaseBadge";
import BooleanBadge from "./BooleanBadge";
import NullBadge from "./NullBadge";
import utils from "../../config-utils";

export default {
    components: {
        BaseBadge,
        BooleanBadge,
        NullBadge
    },
    props: {
        disableTooltip: {
            type: Boolean,
            default: false
        },
        value: {
            type: Array | String | Boolean | null,
            default: ''
        },
        manualLabel: {
            type: String,
            default: ''
        },
        manualIcon: {
            type: String,
            default: ''
        },
        manualTooltip: {
            type: String,
            default: ''
        }
    },
    computed: {
        items() {
            if ((typeof this.value === 'boolean') || (this.value === null)) {
                return [this.value];
            }

            let items = typeof this.value === 'string' ? [this.value + ''] : this.value;

            // Cast 1.000 to 1...
            return items.map(ele => ele.endsWith('.000') ? ele.split('.')[0] : ele);
        },
    },
    methods: {
        ...utils,
        getComponent(value) {
            if (typeof value === 'boolean') {
                return BooleanBadge;
            }

            if (value === null) {
                return NullBadge;
            }

            value = '' + value;
            if (value.startsWith('bool|')) {
                return BooleanBadge;
            }

            if (value.startsWith('null|')) {
                return NullBadge;
            }

            if (value.includes('[[') && value.endsWith(']]')) {
                return SyntaxLoaderBadge;
            }

            if (value.includes('|')) { // Pipe Notation
                return PipeLoaderBadge;
            }

            return NoBadge;
        },
        getLabel(component, syntax) {
            // Evtl. Label aus der Syntax entfernen.
            let returnObj = this.getSyntaxParts(syntax);

            // Syntax ohne evtl. Label
            syntax = returnObj.syntax;

            if (returnObj.label) {
                return returnObj.label;
            }

            return syntax;
        },
        cleanValue(value) {
            if ((value === null) || (typeof value === 'boolean')) {
                return value;
            }

            return (value || '').replace(/[\[\]']+/g, '');
        }
    }
};
</script>

