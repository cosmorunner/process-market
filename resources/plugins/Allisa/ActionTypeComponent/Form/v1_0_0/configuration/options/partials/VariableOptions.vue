<template>
    <div>
        <label class="mb-0">Optionen aus Variable</label>
        <div class="form-group input-group-sm mb-2">
            <select class="form-control" v-model="variable" :disabled="!editable">
                <option value="">Keine Variable</option>
                <option v-for="variable in variables" :value="variable.identifier | variablePipeNotation">
                    {{ variable.identifier }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>

import {mapGetters} from 'vuex';
import utils from "../../../../../../../../js/config-utils";

export default {
    props: {
        field: Object,
        editable: Boolean,
    },
    data() {
        let variable = this.field.variable || {};
        return {
            variable: {...variable}
        };
    },
    filters: {
        variablePipeNotation(value) {
            return 'variable|' + value;
        }
    },
    computed: {
        ...mapGetters([
            'ui',
            'environment_variables',
        ]),
        variables() {
            return this.environment_variables.filter(v => v.type === 'TYPE_JSON');
        }
    },
    methods: {
        ...utils,
    },
    watch: {
        variable: {
            handler: function (newVal) {
                this.$emit('property-change', 'variable', newVal);
            },
            deep: true
        }
    },
    mounted() {
        this.variable = this.field.variable ?? {};
    },
};
</script>
