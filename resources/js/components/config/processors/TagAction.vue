<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.tag">
                Tag: {{options.tag}}
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurde kein Tag-Name angegeben oder der Tag hat ein ungültiges Format.</i></span>
            </li>
        </ul>
    </div>
</template>

<script>

import OptionBadgesWithText from "../../utils/OptionBadgesWithText";

export default {
    components: {
        OptionBadgesWithText
    },
    props: {
        options: Object
    },
    computed: {
        isValid() {
            return (this.options.tag || '').length && this.options.tag.match('^([a-z0-9_]+)$');
        }
    }
};
</script>
