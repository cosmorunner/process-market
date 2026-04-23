<template>
    <div>
        <AutocompleteSelector :items="options.input ? [options.input] : []"
                              :label="'Artefakt'"
                              :action-type="actionType"
                              :max-items="1"
                              :outputs-from-actiontype-only="true"
                              :syntax-include="['action.outputs', 'process.outputs', 'reference.outputs']"
                              :editable="ui.editable"
                              @items-changed="$emit('option-change', 'input', $event.length ? $event[0] : null)"
        />
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="action_data_output">Ausgabe</label>
            <select class="form-control" id="action_data_output"
                    @change="(e) => $emit('option-change', 'action_data_output', e.target.value)"
                    :disabled="!ui.editable"
                    :value="options.action_data_output">
                <option value="">-</option>
                <option v-for="output in actionType.outputs" :value="output.name">
                    {{ 'Aktions-Daten - ' + output.name }}
                </option>
            </select>
            <small class="text-muted">Model-Pipe-Notation des Artefaktes auf ein Aktions-Datenfeld schreiben.</small>
        </div>
    </div>
</template>

<script>

import AutocompleteSelector from "../../utils/AutocompleteSelector";
import {mapGetters} from "vuex";

export default {
    components: {AutocompleteSelector},
    props: {
        options: Object | Array,
        actionType: Object,
        outputs: Object | Array
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
    }
};
</script>
