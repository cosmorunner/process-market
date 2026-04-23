<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="type">Typ</label>
            <select class="form-control" id="type" name="type"
                    @change="(e) => $emit('option-change', e.target.name, e.target.value)"
                    :disabled="!ui.editable"
                    :value="options.type">
                <option value="info">Information</option>
                <option value="success">Erfolgsmeldung</option>
                <option value="warning">Warnung</option>
                <option value="danger">Wichtiger Hinweis</option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Nachricht</label>
            <textarea class="form-control" rows="3"
                      @change="$emit('option-change', 'message', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.message"></textarea>
            <OptionBadgesWithText :value="options.message" display-block hide-on-empty />
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.outputs', 'action.outputs', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendMessage($event)"
            />
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Button-Label</label>
            <textarea class="form-control" rows="1"
                      @change="$emit('option-change', 'button_label', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.button_label"></textarea>
            <OptionBadgesWithText :value="options.button_label" display-block hide-on-empty />
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.meta', 'process.outputs', 'action.outputs', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendButtonLabel($event)"
            />
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Button-Url</label>
            <textarea class="form-control" rows="1"
                      @change="$emit('option-change', 'button_url', $event.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.button_url"></textarea>
            <OptionBadgesWithText :value="options.button_url" display-block hide-on-empty />
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.meta', 'process.outputs', 'action.outputs', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendButtonUrl($event)"
            />
        </div>
        <small class="text-muted d-block">Relative URL mit "/" beginnen.</small>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";
import {mapGetters} from "vuex";

export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector
    },
    props: {
        options: Object,
        outputs: Object | Array,
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
    },
    methods: {
        ...utils,
        appendButtonLabel(item) {
            let appended = this.options.button_label === null ? item.value_with_label : this.options.button_label + item.value_with_label;

            this.$emit('option-change', 'button_label', appended);
        },
        appendButtonUrl(item) {
            let appended = this.options.button_url === null ? item.value_with_label : this.options.button_url + item.value_with_label;

            this.$emit('option-change', 'button_url', appended);
        },
        appendMessage(item) {
            let appended = this.options.message === null ? item.value_with_label : this.options.message + item.value_with_label;

            this.$emit('option-change', 'message', appended);
        },
    }
};
</script>
