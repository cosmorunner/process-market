<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.processes.length">
                Prozesse:
                <OptionBadges :value="options.processes"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.relation_type">
                Verknüpfungstyp:
                <OptionBadges :value="options.relation_type"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es werden keine Verknüpfungen entfernt, weil keine Prozesse oder kein Verknüpfungstyp ausgewählt wurden.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";

export default {
    components: {
        OptionBadges
    },
    props: {
        options: Object
    },
    computed: {
        isValid() {
            return this.options.processes.length || this.options.relation_type;
        }
    }
};
</script>
