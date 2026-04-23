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
            <small class="text-muted">Datenfeld-Name erforderlich. Wird genutzt wenn des primäre und sekundäre Datenfeld nicht gesetzt ist.</small>
        </div>
        <ItemsOptions :items="column.type_options.items" @items-change="onItemsChange" :editable="editable"/>
    </div>
</template>

<script>

import ItemsOptions from "./partials/ItemsOptions.vue";
export default {
    components: {
        ItemsOptions
    },
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
        },
        onItemsChange(items) {
            this.$emit('type-options-change', 'items', items);
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
