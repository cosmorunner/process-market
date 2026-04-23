<template>
    <div>
        <div class="form-group input-group-sm">
            <label class="mb-0">Format</label>
            <select class="form-control" v-model="field.output_format" :disabled="!editable">
                <option value="DD.MM.YYYY">DD.MM.YYYY</option>
                <option value="DD-MM-YYYY">DD-MM-YYYY</option>
            </select>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        field: Object,
        inputOutputNames: Array,
        definition: Object,
        editable: Boolean
    },
};
</script>

