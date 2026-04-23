<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="template">Eigene Logik-Vorlage</label>
            <select class="form-control" @change="templateChanged" :value="templateId" :disabled="!ui.editable">
                <option v-for="template in logicTemplates" :value="template.id">
                    {{ template.name }}
                </option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="validation_error_message">Meldung bei Validierungsfehler</label>
            <textarea class="form-control" rows="2"
                      @change="$emit('option-change', 'validation_error_message', $event.target.value)"
                      :readonly="!ui.editable" v-bind:value="options.validation_error_message"></textarea>
            <OptionBadgesWithText :value="options.validation_error_message" display-block hide-on-empty/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector :action-type="actionType" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'action.outputs', 'auth', 'reference.outputs', 'date']"
                              v-if="ui.editable" @selected="appendValidationErrorMessage($event)"/>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText.vue";
import DropdownSelector from "../../utils/DropdownSelector.vue";

export default {
    components: {
        DropdownSelector,
        OptionBadgesWithText
    },
    props: {
        options: Object,
    },
    computed: {
        ...mapGetters([
            'templates',
            'ui'
        ]),
        logicTemplates() {
            return this.templates.filter(ele => ele.type === 'custom_logic');
        },
        templateId() {
            if (!this.options.template) {
                return null;
            }

            return this.getSyntaxParts(this.options.template).key;
        }
    },
    methods: {
        ...utils,
        templateChanged(e) {
            let template = this.templates.find(ele => ele.id === e.target.value);

            if (template) {
                this.$emit('option-change', 'template', 'template|' + template.id + '[Vorlage - ' + template.name + ']');
                this.$emit('option-change', 'template_name', template.name);
            }
        },
        appendValidationErrorMessage(item) {
            let appended = this.options.validation_error_message === null ? item.value_with_label : this.options.validation_error_message + item.value_with_label;

            this.$emit('option-change', 'validation_error_message', appended);
        },
    }
};
</script>
