<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent" v-if="options.url">
                URL:
                <OptionBadgesWithText :value="options.url" />
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wurde keine URL eingetragen.</i></span>
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
            return (this.options.url || '').length;
        }
    }
};
</script>
