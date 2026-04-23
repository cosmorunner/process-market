<template>
    <div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.profile">
                Profil:
                <OptionBadges :value="options.profile"/>
            </li>
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="Object.keys(filteredProfileParameters).length">
                Parameters:
                <ul>
                    <li v-for="(value, key) in filteredProfileParameters" :key="key">
                        {{ key }}: {{ value }}
                    </li>
                </ul>
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
        filteredProfileParameters() {
            return Object.fromEntries(Object.entries(this.options.profile_parameters).filter(([_, value]) => value !== ""));
        }
    },
    methods: {}
};
</script>
