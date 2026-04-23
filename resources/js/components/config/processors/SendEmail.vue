<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.to.length">
                Empfänger:
                <OptionBadges :value="options.to"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.cc.length">
                CC:
                <OptionBadges :value="options.cc"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.bcc.length">
                BCC:
                <OptionBadges :value="options.bcc"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.subject">
                Betreff: <OptionBadgesWithText :value="options.subject"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.template">
                Vorlage: <span class="text-muted">{{ options.template_name }}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.attachments.length">
                Anhänge:
                <OptionBadges :value="options.attachments"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent">
                Priorität:
                <span class="text-muted">{{+options.priority == 1 ? 'Hoch' : 'Normal'}}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="Object.keys(options.mapping || {}).length">
                Prozessor-Daten-Mapping:
                <template v-for="(value, name) in options.mapping || {}">
                    <span class="d-inline-block mr-2">
                        <OptionBadges :value="value"/> &#x27A1;
                        <span data-toggle="tooltip" data-placement="top" title="Prozess-Daten" class="badge badge-light text-muted" style="font-size: 90%;">
                            <span class="material-icons">grain</span>
                            <span>{{name}}</span>
                        </span>
                    </span>
                </template>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurden keine Empfänger oder eine Vorlage ausgewählt.</i></span>
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
        OptionBadges
    },
    props: {
        options: Object
    },
    computed: {
        isValid() {
            return (this.options.to.length || this.options.cc.length || this.options.bcc.length) && this.options.template;
        }
    }
};
</script>
