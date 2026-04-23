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
        <AutocompleteSelector :items="options.recipients"
                              :icon="'group'"
                              :label="'Empfänger'"
                              :action-type="actionType"
                              :max-items="5"
                              :outputs-from-actiontype-only="true"
                              :syntax-include="['action.outputs', 'process.outputs', 'auth', 'reference.outputs']"
                              :pipe-include="['roles', 'environment_groups', 'environment_users']"
                              :editable="ui.editable"
                              @items-changed="$emit('option-change', 'recipients', $event)"
        />
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Nachricht</label>
            <textarea class="form-control" id="message" rows="1"
                      @change="(e) => $emit('option-change', 'message', e.target.value)"
                      :readonly="!ui.editable"
                      v-bind:value="options.message"></textarea>
            <OptionBadgesWithText :value="options.message" display-block hide-on-empty />
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector
                :action-type="actionType"
                :outputs-from-actiontype-only="true"
                :syntax-include="['process.outputs', 'action.outputs', 'auth', 'reference.outputs']"
                v-if="ui.editable"
                @selected="appendMessage($event)"
            />
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="button_label">Button-Label</label>
            <input type="text" class="form-control" id="button_label" name="button_label" :value="options.button_label"
                   :readonly="!ui.editable"
                   @change="(e) => $emit('option-change', e.target.name, e.target.value)" maxlength="40"/>
            <small class="text-muted">Leer lassen um keinen Button anzuzeigen.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="button_url">Button-Url</label>
            <select class="form-control" id="button_url" name="button_url"
                    @change="(e) => $emit('option-change', e.target.name, e.target.value)"
                    :disabled="!ui.editable"
                    :value="options.button_url">
                <option value="[[process.meta.url((Prozess-URL - Übersicht))]]">URL zum Prozess</option>
                <option value="[[action.path((Aktions-URL - Übersicht))]]">URL zur Aktion</option>
            </select>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";
import {mapGetters} from "vuex";

export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector,
        AutocompleteSelector
    },
    props: {
        options: Object,
        actionType: Object
    },
    computed: {
        ...mapGetters([
            'ui',
        ])
    },
    methods: {
        ...utils,
        appendMessage(item) {
            let appended = this.options.message === null ? item.value_with_label : this.options.message + item.value_with_label;

            this.$emit('option-change', 'message', appended);
        },
    }
};
</script>
