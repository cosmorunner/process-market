<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">URL</label>
            <textarea class="form-control" rows="1"
                      @input="$emit('option-change', 'url', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.url"></textarea>
            <div class="bg-light p-2" v-if= "options.url">
                <OptionBadgesWithText :value="options.url" />
            </div>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.meta', 'system', 'graphs.urls', 'public_apis', 'process.urls', 'url', 'reference.urls', 'process.outputs', 'action.outputs', 'reference.outputs', 'reference.meta', 'action_type_processors']"
                v-if="ui.editable"
                @selected="appendUrl($event)"
            />
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";

export default {
    components: {OptionBadgesWithText, DropdownSelector},
    props: {
        options: Object,
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'definition',
            'relation_types_with_single_process',
            'graphs_output_names',
            'graphs_status_types',
            'processors',
            'ui'
        ])
    },
    methods: {
        ...utils,
        appendUrl(item) {
            let appended = this.options.url === null ? item.value_with_label : this.options.url + item.value_with_label;

            this.$emit('option-change', 'url', appended);
        }
    }
};
</script>
