<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="task">Aufgabe</label>
            <select class="form-control" id="task" name="task" @change="taskChanged" :value="taskIdentifier" :disabled="!ui.editable">
                <option value="">Bitte wählen...</option>
                <option v-for="task in environment_tasks" :value="task.identifier">
                    {{ task.identifier }}
                </option>
            </select>
        </div>
        <div>
            <label class="mb-2">Parameter</label>
            <template v-for="(dataValue, dataKey) in options.parameters || {}">
                <div class="row mb-1" v-if="ui.editable">
                    <div class="col-4">
                        {{dataKey}}
                    </div>
                    <div class="col-7">
                        <OptionBadgesWithText :value="dataValue" v-if="dataValue.length"/>
                        <span v-else class="text-muted">
                            <i>Leere Zeichenkette</i>
                        </span>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-sm btn-outline-danger rounded-left ml-1" @click="onDeleteParameter(dataKey)">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </div>
            </template>
            <div class="row mt-3" v-if="ui.editable">
                <div class="col-4">
                    <input class="form-control form-control-sm" aria-label="Label" v-model="newKey" placeholder="Name..."/>
                    <small class="text-muted">Nur a-z, 0-9 und Unterstrich.</small>
                </div>
                <div class="col-7">
                    <textarea class="form-control mb-2" aria-label="Label" v-model="newValue"/>
                    <div class="input-group-append">
                        <DropdownSelector
                            :action-type="actionType"
                            :outputs-from-actiontype-only="true"
                            :syntax-include="Object.keys(syntaxLoaderLabels())"
                            v-if="ui.editable"
                            @selected="appendValue($event)"
                        />
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn btn-sm btn-outline-success rounded-left ml-1" @click="onAddParameter">
                        <span class="material-icons">add</span>
                    </button>
                </div>
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
            'environment_tasks',
            'ui'
        ]),
        taskIdentifier() {
            if (!this.options.task) {
                return null;
            }

            return this.getSyntaxParts(this.options.task).key;
        }
    },
    methods: {
        ...utils,
        taskChanged(e) {
            let task = this.environment_tasks.find(ele => ele.identifier === e.target.value);

            if (!task) {
                return;
            }

            let withLabel = this.setSyntaxLabel('task|' + task.identifier, 'Aufgabe - ' + task.identifier);

            this.$emit('option-change', 'task', withLabel);
        },
        onAddParameter() {
            if (!this.newKey.trim()) {
                return;
            }

            this.$emit('option-change', 'parameters', {
                ...this.options.parameters,
                [this.newKey.trim()]: this.newValue
            });

            this.newKey = '';
            this.newValue = '';
        },

        onDeleteParameter(name) {
            let parameters = {...this.options.parameters};

            delete parameters[name];

            this.$emit('option-change', 'parameters', parameters);
        },
        appendValue(item) {
            this.newValue = this.newValue + item.value_with_label;
        }
    }
};
</script>
