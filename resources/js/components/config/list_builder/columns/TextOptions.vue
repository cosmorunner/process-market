<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="dataField">Datumsformat</label>
            <select class="form-control" id="dataField" @change="onChangeDateFormat"
                    :value="column.type_options.date_format || ''" :disabled="!editable">
                <option value="">Kein Datumsformat</option>
                <option value="diffForHumans">Differenz zur aktuellen Uhrzeit</option>
                <option value="d.m.Y">DD.MM.YYYY (z.B. 24.08.2011)</option>
                <option value="d.m.Y H:i">DD.MM.YYYY H:M (z.B. 24.08.2011 13:37)</option>
                <option value="d.m.Y H:i:s">DD.MM.YYYY H:M:S (z.B. 24.08.2011 13:37:00)</option>
                <option value="d-m-Y">DD-MM-YYYY (z.B. 24-08-2011)</option>
                <option value="d-m-Y H:i">DD-MM-YYYY H:M (z.B. 24-08-2011 13:37)</option>
                <option value="d-m-Y H:i:s">DD-MM-YYYY H:M:S (z.B. 24-08-2011 13:37:00)</option>
                <option value="H:i">Uhrzeit (hh:mm)</option>
            </select>
        </div>
        <div class="form-group mt-2 mb-0">
            <HideOptions :alias-labels="aliasLabels"
                         :used-column-aliases="usedColumnAliases"
                         :hide="hide"
                         v-on="$listeners"
            />
        </div>
        <hr class="mt-3">
    </div>
</template>

<script>

import HideOptions from "./partials/HideOptions";

export default {
    components: {HideOptions},
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    computed: {
        hide() {
            return this.column.type_options.hide || [];
        }
    },
    methods: {
        onChangeDateFormat(e) {
            let format = e.target.value;
            let typeOptions = {...this.column.type_options};

            if (format === '') {
                delete typeOptions['date_format'];
                this.$emit('property-change', 'type_options', typeOptions);
            }
            else {
                this.$emit('type-options-change', 'date_format', format);
            }
        }
    }
};
</script>
