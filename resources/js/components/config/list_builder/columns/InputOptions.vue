<template>
    <div>
        <div class="form-group mb-2">
            <label class="mb-0" for="custom-key">Datenfeld-Name</label>
            <div class="input-group input-group-sm">
                <input type="text" :class="'form-control' + (invalidCustomKey ? ' is-invalid' : '')" :readonly="!editable"
                       aria-label="Placeholder" v-model="customKey" id="custom-key">
            </div>
            <small class="text-muted">Nur a-z und "_". Datenfeld. Darf keinem existierendem Datenfeld entsprechen.</small>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0" for="default">Wert</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" aria-label="Standard" :value="defaultValue" id="default" :readonly="!editable"
                       @input="onDefaultInput">
            </div>
            <small class="text-muted">Wird genutzt wenn des primäre und sekundäre Datenfeld nicht gesetzt ist.</small>
        </div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0" for="buttonType">Typ</label>
            <select class="form-control" id="buttonType" @change="onTypeChange" :value="type" :disabled="!editable">
                <option value="text">Text</option>
                <option value="email">E-Mail</option>
                <option value="number">Zahl</option>
            </select>
        </div>
        <div class="form-group mb-2">
            <label class="mb-0" for="label">Platzhalter</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" aria-label="Placeholder" :value="placeholder" id="label" :readonly="!editable"
                       @input="onPlaceholderInput">
            </div>
        </div>
        <hr class="mt-3">
    </div>
</template>

<script>

export default {
    props: {
        column: Object,
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        editable: Boolean
    },
    data() {
        return {
            invalidCustomKey: false,
            customKey: this.column.type_options.custom_data_name
        };
    },
    computed: {
        type: function () {
            return this.column.type_options.type || 'text';
        },
        placeholder: function () {
            return this.column.type_options.placeholder || '';
        },
        defaultValue: function () {
            return this.column.type_options.default || '';
        }
    },
    methods: {
        onTypeChange: function (e) {
            this.$emit('type-options-change', 'type', e.target.value);
        },
        onPlaceholderInput: function (e) {
            this.$emit('type-options-change', 'placeholder', e.target.value.trim());
        },
        onDefaultInput: function (e) {
            this.$emit('type-options-change', 'default', e.target.value.trim());
        }
    },
    watch: {
        customKey(newVal) {
            let value = newVal.trim();

            if (value.length && !/^[a-z0-9_]+$/.test(value)) {
                this.invalidCustomKey = true;

                return;
            }

            this.invalidCustomKey = false;

            this.$emit('type-options-change', 'custom_data_name', value);
        }
    }
};
</script>
