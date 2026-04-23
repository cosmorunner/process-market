<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Typ: <span class="text-muted">PDF-Dokument</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.name_format">
                Name:
                <OptionBadgesWithText :value="options.name_format"/>
                <span class="text-muted">.{{ options.type }}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent">
                Vorlage: <span class="text-muted">{{ options.template_name }}</span>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span
                    class="text-danger"><i>Es wird kein Dokument erzeugt, weil keine HTML-Vorlage ausgewählt wurde.</i></span>
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
            return !!this.options.template;
        },
        format() {
            let items = this.options.name_format.match(/\[\[([^\]]*)]]/g);

            if (Array.isArray(items)) {
                return items;
            }

            return this.options.name_format;
        }
    }
};
</script>
