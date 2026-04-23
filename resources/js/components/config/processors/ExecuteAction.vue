<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Prozess:
                <OptionBadges :value="options.process"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent">
                Aktionstyp: <OptionBadges :value="options.action_type"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="Object.keys(options.mapping).length">
                <span>Aktions-Daten-Mapping:</span>
                <template v-for="(value, name) in options.mapping">
                    <OptionBadges :manual-icon="'flash_on'" :manual-label="name"
                                  :manual-tooltip="'Ziel-Aktions-Datensatz'"/>
                </template>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wird keine Aktion ausgeführt weil keine Prozess-Instanz oder Aktionstyp ausgewählt wurde.</i></span>
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
            return this.options.process && this.options.action_type;
        }
    }
};
</script>
