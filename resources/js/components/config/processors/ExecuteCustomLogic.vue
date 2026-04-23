<template>
    <div>
        <ul v-if="isValid" class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                Vorlage: <OptionBadges :value="options.template"/>
            </li>
        </ul>
        <ul v-else class="list-group list-group-flush">
            <li class="list-group-item px-2 py-1 bg-transparent">
                <span class="text-danger"><i>Es wird keine Logik ausgeführt, weil keine "Eigene Logik"-Vorlage ausgewählt wurde.</i></span>
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
        isValid() {
            return this.options.template;
        }
    }
};
</script>
