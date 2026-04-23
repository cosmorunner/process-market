<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="!options.is_public_role || options.recipients.length">
                Benutzer/Gruppen/Rollen:
                <OptionBadges :value="options.recipients"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.is_public_role">
                Öffentliche Rolle:
                <OptionBadges :value="options.role" v-if="options.role"/>
                <span v-else>
                    Alle öffentliche Rollen
                </span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="!options.is_public_role && options.role">
                Rollen-Filterung:
                <OptionBadges :value="options.role"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span
                    class="text-danger"><i>Es werden keine Zugriffe entfernt, weil keine Prozesse ausgewählt wurden.</i></span>
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
            if (this.options.is_public_role) {
                return true;
            }

            return this.options.recipients.length;
        }
    }
};
</script>
