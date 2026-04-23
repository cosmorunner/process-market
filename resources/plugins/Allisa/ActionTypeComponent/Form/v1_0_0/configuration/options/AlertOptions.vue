<template>
    <div>
        <div class="form-group mb-2">
            <label for="alert" class="mb-0">Text</label>
            <input type="text" class="form-control form-control-sm" id="alert" v-model="field.default" aria-describedby="helper_text" :readonly="!editable"/>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Typ</label>
            <div class="dropdown">
                <button type="button" :class="'btn btn-sm dropdown-toggle btn-outline-' + field.alert_type"
                        role="button" id="alertTypeDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="!editable">
                    <span :class="'material-icons text-' + field.alert_type">lens</span> {{ labels(field.alert_type) }}
                </button>
                <div class="dropdown-menu" aria-labelledby="alertTypeDropdown">
                    <button type="button" class="dropdown-item" @click="onChangeAlertType('success')">
                        <span class="material-icons text-success">lens</span>
                        {{ labels('success') }}
                    </button>
                    <button type="button" class="dropdown-item" @click="onChangeAlertType('info')">
                        <span class="material-icons text-info">lens</span>
                        {{ labels('info') }}
                    </button>
                    <button type="button" class="dropdown-item" @click="onChangeAlertType('warning')">
                        <span class="material-icons text-warning">lens</span>
                        {{ labels('warning') }}
                    </button>
                    <button type="button" class="dropdown-item" @click="onChangeAlertType('danger')">
                        <span class="material-icons text-danger">lens</span>
                        {{ labels('danger') }}
                    </button>
                </div>
            </div>
        </div>
        <ComputedInputOptions :computed-input="computedInput" v-on="$listeners" :editable="editable"/>
    </div>
</template>
<script>

import ComputedInputOptions from "./partials/ComputedInputOptions";

export default {
    components: {ComputedInputOptions},
    props: {
        field: Object,
        inputOutputNames: Array,
        definition: Object,
        editable: Boolean
    },
    computed: {
        computedInput: function () {
            return (this.field.computed_input || '').trim();
        }
    },
    methods: {
        labels(type) {
            return {
                'success': 'Erfolgsmeldung',
                'info': 'Information',
                'warning': 'Warnung',
                'danger': 'Sehr wichtiger Hinweis'
            }[type] || '?';
        },
        onChangeAlertType(type) {
            this.$emit('property-change', 'alert_type', type);
        }
    }
};
</script>

