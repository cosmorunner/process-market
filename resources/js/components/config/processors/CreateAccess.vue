<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li v-if="!options.is_public_role" class="list-group-item px-2 py-1 bg-transparent">
                Benutzer/Gruppen:
                <OptionBadges :value="options.recipients"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.role">
                {{ options.is_public_role ? 'Öffentliche Rolle' : 'Rolle' }}:
                <OptionBadges :value="options.role"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurden keine Benutzer, Gruppe oder Rolle ausgewählt.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadges from "../../utils/OptionBadges";
import ModelBadge from "../../utils/ModelBadge";

export default {
    components: {
        ModelBadge,
        OptionBadges
    },
    props: {
        options: Object
    },
    computed: {
        isValid() {
            let validation = this.options.role !== null && this.options.role.length;
            if (!this.options.is_public_role) {
                validation = this.options.recipients.length && validation;
            }

            return validation;
        }
    }
};
</script>
