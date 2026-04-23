<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.input">
                Artefakt:
                <OptionBadges :value="options.input"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.action_data_output">
                Aktions-Datenfeld-Ausgabe:
                <OptionBadges :value="options.action_data_output"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wird kein Artefakt kopiert, weil keine Eingabe und/oder Ausgabe definiert wurden.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";

export default {
    components: {OptionBadges},
    props: {
        options: Object | Array
    },
    computed: {
        isValid() {
            return this.options.input && this.options.action_data_output
        },
    }
};
</script>
