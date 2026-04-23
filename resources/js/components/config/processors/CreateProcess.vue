<template>
    <div>
        <ul class="list-group list-group-flush" v-if="isValid">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Prozesstyp: <span class="text-muted">{{ options.process_type }}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.role">
                Benutzer-Rolle: <OptionBadges :value="options.role"/>
            </li>
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
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="Object.keys(options.mapping).length">
                Output-Mapping:
                <div class="mt-1">
                    <template v-for="(value, name) in options.mapping">
                        <span class="d-block mb-1">
                            <span data-toggle="tooltip" data-placement="top" title="Prozess-Daten" class="badge badge-light text-muted" style="font-size: 90%;">
                                <span class="material-icons">grain</span>
                                <span>{{name}}</span>
                            </span>
                            &#x27A1;
                            <OptionBadgesWithText :value="value"/>
                        </span>
                    </template>
                </div>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.output">
                Aktions-Daten Ausgabe:
                <OptionBadges :value="options.output"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wird keine Prozess-Instanz erzeugt, weil kein Prozesstyp ausgewählt wurde.</i></span>
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
        isValid() {
            return this.options.process_type;
        }
    }
};
</script>
