<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Event: <OptionBadges :value="options.event" />
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent d-flex" v-if="Object.keys(options.data).length">
                <span>Daten:</span>
                <div class="ml-1">
                    <template v-for="(value, name) in options.data">
                        <div class="mb-1">
                            <OptionBadges :manual-icon="'flash_on'" :manual-label="name" :manual-tooltip="''"/>
                            <span>&#x27A1;</span>
                            <OptionBadgesWithText :value="value"/>
                        </div>
                    </template>
                </div>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurde kein Event ausgewählt.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText.vue";

export default {
    components: {
        OptionBadgesWithText,
        OptionBadges
    },
    props: {
        options: Object
    },
    computed: {
        isValid() {
            return this.options.event;
        }
    }
};
</script>
