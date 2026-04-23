<template>
    <div>
        <ul class="list-group list-group-flush" v-if="isValid">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Prozesstyp: <span class="text-muted">{{ options.process_type }}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.role">
                Benutzer-Rolle: <span class="text-muted">{{ options.role_name }}</span>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.name">
                Name:
                <OptionBadges :value="options.name"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.description">
                Beschreibung:
                <OptionBadges :value="options.description"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.tags">
                Tags:
                <OptionBadges :value="options.tags"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.image">
                Icon:
                <OptionBadges :value="options.image"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.reference">
                Referenz:
                <OptionBadges :value="options.reference"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="Object.keys(options.mapping).length">
                Output-Mapping: <span class="badge badge-light text-muted mr-1"
                                      v-for="(value, name) in options.mapping">
                <span data-toggle="tooltip" data-placement="top" title="Aktions-Daten"><span class="material-icons">flash_on</span> {{ value }}</span> &#x27A1;
                <span data-toggle="tooltip" data-placement="top" title="Prozess-Daten"><span class="material-icons">grain</span> {{ name }}</span></span>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-muted"><i>Es wird keine Prozess-Instanz erzeugt, weil kein Prozesstyp ausgewählt wurde.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";

export default {
    components: {OptionBadges},
    props: {
        options: Object
    },
    computed: {
        isValid() {
            return this.options.process_type;
        }
    }
};
</script>
