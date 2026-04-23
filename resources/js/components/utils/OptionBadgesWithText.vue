<template>
    <div :class="displayBlock ? 'd-block' : 'd-inline-block'">
        <!-- In einem Block mit Hintergrund -->
        <div v-if="displayBlock">
            <div class="bg-light px-2 py-1" v-if="syntaxes.length">
                <OptionBadges :value="parts"/>
            </div>
        </div>

        <!-- Als einfacher inline String -->
        <div class="d-inline-block" v-else>
            <OptionBadges :value="parts"/>
        </div>
    </div>
</template>

<script>

import OptionBadges from "./OptionBadges";
import utils from "../../config-utils";

export default {
    components: {OptionBadges},
    props: {
        disableTooltip: {
            type: Boolean,
            default: false
        },
        hideOnEmpty: {
            type: Boolean,
            default: false
        },
        displayBlock: {
            type: Boolean,
            default: false
        },
        value: {
            type: String | null,
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
        syntaxes() {
            if (!this.value) {
                return [];
            }

            let syntaxes = [];
            // get all values that are either bracked in [[]] or that contain '|'
            const regex = /\[\[(.*?)]]|(\S*\|\S*)/g;
            let match;

            while ((match = regex.exec(this.value)) !== null) {
                if (match[1]) {
                    syntaxes.push(`[[${match[1]}]]`);
                }

                else {
                    if (match[2]) {
                        syntaxes.push(match[2]);
                    }
                }
            }

            return syntaxes;
        },
        parts() {
            if (!this.value) {
                return [];
            }

            let replaced = this.value;
            let parts = [];

            this.syntaxes.forEach(function (syntax) {
                replaced = replaced.replace(syntax, '#-#');
            });

            let replacedParts = replaced.split('#-#');

            for (let i = 0; i < replacedParts.length; i++) {
                parts.push(replacedParts[i]);
                parts.push(this.syntaxes[i]);
            }

            return parts.filter(ele => typeof ele === 'string' && ele.length);
        },
    },
    methods: {
        ...utils
    }
};
</script>

