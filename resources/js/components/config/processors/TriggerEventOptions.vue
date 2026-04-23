<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="event">Event</label>
            <select class="form-control" id="event" name="event" @change="eventChanged" :value="eventName" :disabled="!ui.editable">
                <option value="">Bitte wählen...</option>
                <option v-for="event in events" :value="event.name">
                    {{ event.name }}
                </option>
            </select>
        </div>
        <div class="mb-3" v-if="Object.keys(options.data).length || (selectedEvent && Object.keys(selectedEvent.data).length)">
            <label class="mb-2">Daten</label>
            <template v-for="(dataValue, dataKey) in options.data || {}">
                <div class="input-group input-group-sm mb-1">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" :disabled="!ui.editable"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>{{ dataKey }}</span>
                        </button>
                        <div class="dropdown-menu scrollable-dropdown">
                            <button type="button" class="dropdown-item"
                                    v-for="newExernalOutputKey in usableEventDataNames"
                                    @click="onChangeKey(newExernalOutputKey, dataKey)">
                                <span>{{ newExernalOutputKey }}</span>
                            </button>
                        </div>
                    </div>
                    <input class="form-control" aria-label="Label" :value="dataValue" @input="onInputValue" :data-key="dataKey" :readonly="!ui.editable"/>
                    <div class="input-group-append">
                        <DropdownSelector
                            :action-type="actionType"
                            :outputs-from-actiontype-only="true"
                            :syntax-include="Object.keys(syntaxLoaderLabels())"
                            v-if="ui.editable"
                            @selected="onChangeValue($event, dataKey)"
                        />
                        <button class="btn btn-outline-danger rounded-left ml-1" @click="onDeleteData(dataKey)" v-if="ui.editable">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </div>
                <OptionBadgesWithText :value="dataValue" display-block />
                <small class="text-muted d-block mb-2" v-if="eventDataDescription(dataKey).length">{{ eventDataDescription(dataKey) }}</small>
            </template>
            <div class="d-flex justify-content-start" v-if="usableEventDataNames.length && ui.editable">
                <button class="btn btn-sm btn-outline-success" @click="onAddData">
                    <span class="material-icons">add</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import utils from "../../../config-utils";
import {mapGetters} from "vuex";
import OptionBadges from "../../utils/OptionBadges";
import DropdownSelector from "../../utils/DropdownSelector";
import OptionBadgesWithText from "../../utils/OptionBadgesWithText.vue";

export default {
    components: {
        OptionBadgesWithText,
        DropdownSelector,
        OptionBadges
    },
    props: {
        actionType: Object,
        options: Object
    },
    data() {
        return {
            newKey: '',
            newValue: ''
        };
    },
    computed: {
        ...mapGetters([
            'events',
            'ui'
        ]),
        selectedEvent() {
            return this.events.find(ele => ele.name === this.eventName) || null;
        },
        usableEventDataNames() {
            if (!this.selectedEvent) {
                return [];
            }

            return (this.selectedEvent.data || []).filter(ele => !Object.keys(this.options.data)
                .includes(ele.name))
                .map(ele => ele.name);
        },
        eventName() {
            if (!this.options.event) {
                return null;
            }

            return this.getSyntaxParts(this.options.event).key;
        }
    },
    methods: {
        ...utils,
        eventChanged(e) {
            let event = this.events.find(ele => ele.name === e.target.value);

            if (!event) {
                return;
            }

            let withLabel = this.setSyntaxLabel('event|' + event.name, 'Event - ' + event.name);

            this.$emit('option-change', 'event', withLabel);
            this.$emit('option-change', 'data', {});
        },
        eventDataDescription(name) {
            if (!this.selectedEvent) {
                return '';
            }

            let dataItem = this.selectedEvent.data.find(ele => ele.name === name);

            if (dataItem) {
                return dataItem.description || '';
            }

            return '';
        },
        onAddData() {
            if (!this.usableEventDataNames.length) {
                return;
            }
            this.$emit('option-change', 'data', {
                ...this.options.data,
                [this.usableEventDataNames[0]]: ''
            });
        },

        onDeleteData(mappingKey) {
            let data = {...this.options.data};

            delete data[mappingKey];

            this.$emit('option-change', 'data', data);
        },
        onChangeKey(newExternalOutputKey, mappingKey) {
            let data = {...this.options.data};
            let value = data[mappingKey];

            delete data[mappingKey];

            data = {
                ...data,
                [newExternalOutputKey]: value
            };

            this.$emit('option-change', 'data', data);
        },
        onInputValue(e) {
            let key = e.target.dataset.key;
            let data = {
                ...(this.options.data || []),
                [key]: e.target.value
            };

            this.$emit('option-change', 'data', data);
        },
        onChangeValue(dropDownItem, key) {

            let data = {
                ...this.options.data,
                [key]: dropDownItem.value_with_label
            };

            this.$emit('option-change', 'data', data);
        },
    }
};
</script>
