<template>
    <div>
        <div>
            <AutocompleteSelector
                :items="options.processes"
                :icon="'grain'"
                :label="'Prozess'"
                :max-items="5"
                :action-type="actionType"
                :syntax-include="['action.outputs', 'process.outputs', 'process.meta', 'reference.outputs', 'auth', 'users']"
                :pipe-include="['relation_types', 'graphs_relation_types']"
                :only-from-action-type="true"
                :editable="ui.editable"
                @items-changed="$emit('option-change', 'processes', $event)"
            />
            <small class="text-muted">Bei einem Verknüpfungstyp werden alle verknüpften Prozesse von diesem Verknüpfungstyp gelöscht.</small>
        </div>
        <div class="mt-3">
            <AutocompleteSelector
                :items="options.related"
                :icon="'settings_ethernet'"
                :label="'Verknüpfte Prozesse löschen'"
                :max-items="5"
                :action-type="actionType"
                :pipe-include="['relation_types', 'graphs_relation_types']"
                :only-from-action-type="true"
                :editable="ui.editable"
                @items-changed="$emit('option-change', 'related', $event)"
            />
            <small class="text-muted">Zusätzlich verknüpfte Prozesse zu den oben ermittelten Prozessen löschen.</small>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import AutocompleteSelector from "../../utils/AutocompleteSelector.vue";
import {mapGetters} from "vuex";

export default {
    components: {AutocompleteSelector},
    props: {
        options: Object,
        outputs: Object | Array,
        actionType: Object
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
    },
    methods: {
        ...utils
    }
};
</script>
