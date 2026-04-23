<template>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <Where :allowed-operators="['json_array_contains', 'json_array_not_contains', 'ilike', 'not_ilike']"
                       :data="data" title="Filter" :support-data="supportData" :used-column-aliases="usedColumnAliases"
                       :alias-labels="aliasLabels" default-column="users.tags" default-operator="json_array_contains"
                       :only-column-aliases="['users_tags', 'users_email']" v-on="$listeners" :editable="ui.editable"/>
                <small class="text-muted">Wird als Wert ein JSON-Array gewählt, muss mindestens ein Wert im JSON-Array
                    des
                    Datenfeld vorhanden sein.</small>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="selectSelection">Meta-Daten</label>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item py-1 pl-1"
                        v-for="item in [...supportData.select].sort((a, b) => a.alias > b.alias ? 1 : -1)">
                        <span v-if="aliasLabels.hasOwnProperty(item.alias)">{{ aliasLabels[item.alias] }} - </span>
                        <span>{{ item.alias }}</span>
                    </li>
                </ul>
                <small class="text-muted">Basis-Daten jedes Benutzers.</small>
            </div>
            <div class="form-group mb-3">
                <div class="d-flex justify-content-between">
                    <label for="processData">Prozess-Identitäts-Daten
                        <span v-if="outputSelect.length" class="badge badge-light">{{ outputSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="outputSelect = []" v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="processData" v-model="outputSelect" size="8"
                        :disabled="!ui.editable">
                    <option v-for="item in outputSelection" :value="item.column + ' as ' + item.alias">
                        {{ (item.label ? item.label + ' - ' : '') + item.alias }}
                    </option>
                </select>
            </div>

            <OrderBy :data="data" :support-data="supportData" :used-column-aliases="sortableColumnAliases"
                     :alias-labels="aliasLabels" v-on="$listeners" :editable="ui.editable"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import utils from "../../../../config-utils";
import {reduxActions} from "../../../../store/develop-and-config";
import DropdownSelector from "../../../utils/DropdownSelector.vue";
import OptionBadgesWithText from "../../../utils/OptionBadgesWithText.vue";
import Where from "../partials/Where.vue";
import OrderBy from "../partials/OrderBy.vue";

export default {
    components: {
        OrderBy,
        Where,
        OptionBadgesWithText,
        DropdownSelector
    },
    props: {
        aliasLabels: Object,
        supportData: Object | null,
        usedColumnAliases: Array | Object,
        data: Object
    },
    data() {
        return {
            outputSupportData: this.supportData.outputs,
            outputSelect: (this.data.source.select || []).filter(ele => ele.startsWith('processes.data'))
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
        sortableColumnAliases() {
            let remove = [
                'users_aliases',
                'users_tags',
                'users_id',
                'users_pipe_notation',
                'users_identity_id',
                'users_identity_pipe_notation',
            ];

            return this.usedColumnAliases.filter(ele => !remove.includes(ele.alias));
        },
        outputSelection() {
            let items = this.outputSupportData;

            for (let item of this.outputSelect) {
                let column = item.split(' as ')[0];
                let alias = item.split(' as ')[1];

                // Falls eine bereits selektierte Status-Spalte NICHT in der Support-Data ist, wird diese manuell
                // hinzugefügt, damit die Anzeige korrekt ist, auch wenn der Benutzer keinen Zugriff mehr
                // auf die Status hat.
                if (!items.find(ele => ele.column === column)) {
                    items.push({
                        label: alias.substring('processes_data_'.length),
                        column: column,
                        alias: alias
                    });
                }
            }

            // Unique items by alias
            items = [
                ...new Map(items.map(item => [
                    item.alias,
                    item
                ])).values()
            ];

            return items.sort((a, b) => a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
    },
    watch: {
        outputSelect(newVal, oldVal) {
            if (newVal.length === oldVal.length && newVal.length === 0) {
                return;
            }

            let items = [...new Set([...this.outputSelect])];

            this.$emit('update-source', 'select', items);
        }
    }
};
</script>