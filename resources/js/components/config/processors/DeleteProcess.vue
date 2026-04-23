<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Prozesse:
                <OptionBadges :value="options.processes"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.related.length">
                Verknüpfte Prozesse:
                <OptionBadges :value="options.related"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurden keine Prozesse oder Verknüpfungstypen ausgewählt.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";

export default {
    components: {OptionBadges},
    props: {
        options: Object,
        definition: Object
    },
    computed: {
        isValid() {
            return this.options.processes.length;
        }
    }
};
</script>
