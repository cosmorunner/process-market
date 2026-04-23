<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Typ: <span class="text-muted">{{ typeLabel(options.type) }}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent">
                Nachricht:
                <OptionBadgesWithText :value="options.message"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.button_label">
                Button-Label:
                <OptionBadgesWithText :value="options.button_label"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.button_url">
                Button-Url:
                <OptionBadgesWithText :value="options.button_url"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wird keine Flash-Message angezeigt versendet, weil die Nachricht leer ist.</i></span>
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
            return this.options.message.length;
        }
    },
    methods: {
        typeLabel(type) {
            return {
                info: 'Information',
                success: 'Erfolgsmeldung',
                warning: 'Warnung',
                danger: 'Wichtige Meldung'
            }[type] || type;
        }
    },
};
</script>
