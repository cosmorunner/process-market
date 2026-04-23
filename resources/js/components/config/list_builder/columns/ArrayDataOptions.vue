<template>
    <div>
        <div class="form-group mb-2">
            <label class="mb-0" for="label">Platzhalter</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" aria-label="Placeholder" :value="placeholder" id="label"
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
    },
    computed: {
        placeholder: function () {
            return this.column.type_options.placeholder || '';
        }
    },
    methods: {
        onPlaceholderInput: function (e) {
            this.$emit('type-options-change', 'placeholder', e.target.value.trim());
        }
    }
};
</script>
