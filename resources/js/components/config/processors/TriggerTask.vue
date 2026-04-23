<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Aufgabe: <OptionBadges :value="options.task" />
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent">
                Benutzer: {{ options.execute_as_auth ? 'Angemeldeter Benutzer' : 'Bot der Aufgabe' }}
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent d-flex" v-if="Object.keys(options.parameters).length">
                <span>Parameter:</span>
                <div class="ml-1">
                    <template v-for="(value, name) in options.parameters">
                        <div class="mb-1">
                            {{ name }}
                            <span>&#x27A1;</span>
                            <OptionBadgesWithText :value="value"/>
                        </div>
                    </template>
                </div>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurde keine Aufgabe ausgewählt.</i></span>
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
            return this.options.task;
        }
    }
};
</script>
