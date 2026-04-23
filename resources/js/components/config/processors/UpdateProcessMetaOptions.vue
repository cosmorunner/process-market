<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Name</label>
            <textarea class="form-control" rows="1" @change="$emit('option-change', 'name', $event.target.value)"
                      :readonly="!ui.editable" v-bind:value="options.name"></textarea>
            <OptionBadgesWithText :value="options.name" display-block hide-on-empty/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector :action-type="actionType" :outputs-from-actiontype-only="true" :dropdown-width="500"
                              :syntax-include="['process.outputs', 'action.outputs',  'process.meta', 'reference.outputs', 'reference.metas', 'date', 'variables']"
                              v-if="ui.editable" @selected="appendName($event)"/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Beschreibung</label>
            <textarea class="form-control" rows="1" @change="$emit('option-change', 'description', $event.target.value)"
                      :readonly="!ui.editable" v-bind:value="options.description"></textarea>
            <OptionBadgesWithText :value="options.description" display-block hide-on-empty/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector :action-type="actionType" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'action.outputs',  'process.meta', 'reference.outputs', 'reference.metas', 'date', 'variables']"
                              v-if="ui.editable" :dropdown-width="500" @selected="appendDescription($event)"/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="message">Referenz</label>
            <span class="px-0 py-1 d-block"><small class="text-danger material-icons">priority_high</small><small
                class="text-muted">
                Referenznamen von Prozess-Instanzen müssen pro Prozesstyp einzigartig sein.
            </small></span>
            <textarea class="form-control" rows="1" @change="$emit('option-change', 'reference', $event.target.value)"
                      :readonly="!ui.editable" v-bind:value="options.reference"></textarea>
            <OptionBadgesWithText :value="options.reference" display-block hide-on-empty/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <DropdownSelector :action-type="actionType" :outputs-from-actiontype-only="true"
                              :syntax-include="['process.outputs', 'action.outputs',  'process.meta', 'reference.outputs', 'reference.meta', 'date', 'variables']"
                              v-if="ui.editable" :dropdown-width="500" @selected="appendReference($event)"/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="image">Icon</label>
            <div class="row no-gutters">
                <div class="col-6 pr-1">
                    <AutocompleteSelector :items="image && image.length ? [options.image] : []"
                                          :action-type="actionType" :max-items="1" :outputs-from-actiontype-only="true"
                                          :dropdown-width="500" :add-only-from-autocomplete="false"
                                          :syntax-include="['process.outputs', 'action.outputs']"
                                          :editable="ui.editable"
                                          @items-changed="$emit('option-change', 'image', $event.length ? $event[0] : '')"/>
                </div>
                <div class="col-6">
                    <IconSelection
                        v-if="image === null || (typeof image === 'string' && !image.startsWith('[[') && !image.includes('|'))"
                        :selected="image" :require-selection="false" :editable="ui.editable"
                        @on-select-icon="$emit('option-change', 'image', $event)"/>
                </div>
            </div>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="tags">Tags</label>
            <AutocompleteSelector :items="options.tags && options.tags.length ? [options.tags] : []"
                                  :action-type="actionType" :max-items="1" :outputs-from-actiontype-only="true"
                                  :add-only-from-autocomplete="false" :dropdown-width="500"
                                  :syntax-include="['process.outputs', 'action.outputs']" :editable="ui.editable"
                                  @items-changed="$emit('option-change', 'tags', $event.length ? $event[0] : '')"/>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import AutocompleteSelector from "../../utils/AutocompleteSelector";
import IconSelection from "../../utils/IconSelection";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText";
import DropdownSelector from "../../utils/DropdownSelector";
import {mapGetters} from "vuex";

export default {
    components: {
        DropdownSelector,
        OptionBadgesWithText,
        IconSelection,
        AutocompleteSelector
    },
    props: {
        options: Object,
        actionType: Object
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        image() {
            return this.options.image;
        }
    },
    methods: {
        ...utils,
        appendName(item) {
            let appended = this.options.name === null ? item.value_with_label : this.options.name + item.value_with_label;

            this.$emit('option-change', 'name', appended);
        },
        appendDescription(item) {
            let appended = this.options.description === null ? item.value_with_label : this.options.description + item.value_with_label;

            this.$emit('option-change', 'description', appended);
        },
        appendReference(item) {
            let appended = this.options.reference === null ? item.value_with_label : this.options.reference + item.value_with_label;

            this.$emit('option-change', 'reference', appended);
        },
    }
};
</script>
