<template>
    <div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.helper_text">
                Beschreibung:
                <span>{{ options.helper_text }}</span>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.connector">
                Connector:
                <OptionBadges :value="options.connector" />
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.request">
                Request:
                <OptionBadges :value="options.request" />
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
    methods: {}
};
</script>
