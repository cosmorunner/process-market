<template>
    <div>
        <AutocompleteSelector :items="options.processes" :label="'Prozess'" :icon="'grain'" :action-type="actionType"
                              :max-items="5" :outputs-from-actiontype-only="true"
                              :syntax-include="['action.outputs', 'process.outputs', 'auth', 'users', 'reference.outputs']"
                              @items-changed="$emit('option-change', 'processes', $event)" :editable="ui.editable"
                              :help="'Leer lassen um alle Verknüpfungen zu Prozessen des ausgewählten Verknüpfungstyp zu entfernen.'"/>
        <AutocompleteSelector :items="options.relation_type ? [options.relation_type] : []" :label="'Verknüpfungstyp'"
                              :icon="'settings_ethernet'" :max-items="1"
                              :pipe-include="['relation_types', 'graphs_relation_types']"
                              @items-changed="onChangeRelationType($event, 'source_relation_type')"
                              :editable="ui.editable"
                              :help="'Leer lassen um alle Verknüpfungen zu den obenstehenden Prozessen zu entfernen.'"/>
    </div>
</template>

<script>

import {mapGetters} from "vuex";
import utils from "../../../config-utils";
import SortedRelationTypes from "../../utils/SortedRelationTypes";
import AutocompleteSelector from "../../utils/AutocompleteSelector";

export default {
    components: {
        AutocompleteSelector,
        SortedRelationTypes
    },
    props: {
        options: Object,
        outputs: Object | Array,
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'relation_types',
            'graphs_relation_types',
            'ui'
        ])
    },
    methods: {
        ...utils,
        onChangeRelationType(autocompleteItems) {
            if (!autocompleteItems.length) {
                this.$emit('option-change', 'relation_type', null);
                this.$emit('option-change', 'relation_type_name', null);

                return;
            }

            let parts = this.getSyntaxParts(autocompleteItems[0]);
            // Erst in external schauen
            let relationType = this.graphs_relation_types.find(ele => ele.namespace === parts.namespace && ele.reference === parts.key);

            // Wenn existiert dann Namespace aus parts übernehmen
            if (!relationType) {
                relationType = this.relation_types.find(ele => ele.reference === parts.key);
            }

            if (relationType) {
                this.$emit('option-change', 'relation_type', autocompleteItems[0]);
                this.$emit('option-change', 'relation_type_name', relationType.name);
            }
            else {
                this.$emit('option-change', 'relation_type', null);
                this.$emit('option-change', 'relation_type_name', null);
            }
        },
    }
};
</script>
