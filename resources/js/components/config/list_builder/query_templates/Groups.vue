<template>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <Where :allowed-operators="['json_array_contains', 'json_array_not_contains', 'ilike', 'not_ilike']"
                       :data="data" title="Filter" :support-data="supportData" :used-column-aliases="usedColumnAliases"
                       :alias-labels="aliasLabels" default-column="groups.tags" default-operator="json_array_contains"
                       :only-column-aliases="['groups_tags', 'groups_name', 'groups_description']" v-on="$listeners"
                       :editable="ui.editable"/>
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
                <small class="text-muted">Basis-Daten jeder Gruppe.</small>
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
    computed: {
        ...mapGetters([
            'ui',
        ]),
        sortableColumnAliases() {
            let remove = [
                'groups_aliases',
                'groups_tags',
                'groups_id',
                'groups_pipe_notation'
            ];

            return this.usedColumnAliases.filter(ele => !remove.includes(ele.alias));
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
    }
};
</script>