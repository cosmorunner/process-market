<template>
    <div>
        <ul class="list-group list-group-flush">
            <template v-if="!isInvalid">
                <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.name">
                    Name: <OptionBadgesWithText :value="options.name"/>
                </li>
                <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.description">
                    Beschreibung: <OptionBadgesWithText :value="options.description"/>
                </li>
                <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.reference">
                    Referenz: <OptionBadgesWithText :value="options.reference"/>
                </li>
                <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.tags">
                    Tags:
                    <OptionBadges :value="options.tags"/>
                </li>
                <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.image">
                    Icon:
                    <OptionBadges :value="options.image"/>
                </li>
            </template>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="isInvalid">
                <span class="text-danger"><i>Es werden keine Prozess-Metadaten geändert.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";

export default {
    components: {
        OptionBadgesWithText,
        OptionBadges},
    props: {
        options: Object
    },
    computed: {
        isInvalid() {
            let optionString = (this.options.name || '')
                + (this.options.description || '')
                + (this.options.reference || '')
                + (this.options.tags || '')
                + (this.options.image || '').trim();

            return optionString === '';
        }
    }
};
</script>
