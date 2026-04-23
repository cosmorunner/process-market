<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Dokument-Name</label>
            <textarea class="form-control" rows="1" @change="$emit('option-change', 'name_format', $event.target.value)"
                      :readonly="!ui.editable" v-bind:value="options.name_format"></textarea>
            <OptionBadgesWithText :value="options.name_format" display-block hide-on-empty/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector :action-type="actionType" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'action.outputs', 'auth', 'reference.outputs', 'date']"
                              v-if="ui.editable" @selected="appendNameFormat($event)"/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="template">HTML-Vorlage</label>
            <select class="form-control" id="template" name="template" :disabled="!ui.editable"
                    @change="templateChanged" :value="options.template">
                <option v-for="template in htmlTemplates" :value="'template|' + template.id">
                    {{ template.name }}
                </option>
            </select>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="action_data_output">Ausgabe</label>
            <select class="form-control" id="action_data_output"
                    @change="$emit('option-change', 'action_data_output', $event.target.value)" :disabled="!ui.editable"
                    :value="options.action_data_output">
                <option value="">-</option>
                <option v-for="output in sortedOutputs"
                        :value="output.name">
                    {{ 'Aktions-Daten - ' + output.name }}
                </option>
            </select>
            <small class="text-muted">Model-Pipe-Notation des Dokumentes auf ein Aktions-Datenfeld schreiben.</small>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";

export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector
    },
    props: {
        options: Object,
        actionType: Object,
    },
    computed: {
        ...mapGetters([
            'templates',
            'ui'
        ]),
        htmlTemplates() {
            return this.templates.filter(ele => ele.type === 'html');
        },
        sortedOutputs() {
            return [...this.actionType.outputs].sort((a, b) => a.name.localeCompare(b.name))
        }
    },
    methods: {
        ...utils,
        appendNameFormat(item) {
            let appended = this.options.name_format === null ? item.value_with_label : this.options.name_format + item.value_with_label;

            this.$emit('option-change', 'name_format', appended);
        },
        templateChanged(e) {
            let id = e.target.value.split('|')[1] || '';
            let template = this.templates.find(ele => ele.id === id);

            if (template) {
                this.$emit('option-change', 'template', 'template|' + template.id);
                this.$emit('option-change', 'template_name', template.name);
            }
        }
    }
};
</script>
