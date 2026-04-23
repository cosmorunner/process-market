<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Prozesse:
                <OptionBadges :value="options.to"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent">
                {{ options.reverse ? 'Ziel-Verknüpfungstyp:' : 'Verknüpfungstyp:' }}
                <OptionBadges :value="options.relation_type"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="Object.keys(options.data ||{}).length">
                Daten:
                <template v-for="(value, key) in options.data">
                    <span class="d-inline-block mr-2">
                        <OptionBadges :value="value"/> &#x27A1;
                        <span>{{ key }}</span>
                    </span>
                </template>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wird keine Verknüpfung erzeugt, weil keine Prozesse oder kein Verknüpfungstyp ausgewählt wurden.</i></span>
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
            return this.options.to.length && this.options.relation_type;
        }
    }
};
</script>
