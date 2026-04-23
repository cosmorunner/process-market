<template>
    <div class="form-group input-group-sm mb-3">
        <label class="mb-0" for="multiple">Mehrfachausführung</label>
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <span class="material-icons">read_more</span>
                </span>
            </div>
            <select class="form-control" id="multiple" name="multiple" :value="multipleValue" :disabled="!editable"
                    @change="(e) => $emit('multiple-value-change', e.target.value)">
                <option value="">Keine Mehrfachausführung</option>
                <option v-for="output in actionType.outputs" :value="'[[action.outputs.' + output.name + ']]'"
                        v-if="output.type === 'array'">
                    Aktions-Daten - {{ output.name }}
                </option>
                <option v-for="output in outputs" :value="'[[process.outputs.' + output.name + ']]'"
                        v-if="output.type === 'array'">
                    Prozess-Daten - {{ output.name }}
                </option>
            </select>
        </div>
        <small class="text-muted">Es werden nur Aktions- und Prozess-Daten vom Typ "JSON-Array" angezeigt.</small>
    </div>
</template>

<script>
export default {
    props: {
        outputs: Array | Object,
        actionType: Object,
        multipleValue: String,
        editable: {
            type: Boolean,
            default: true
        }
    }
};
</script>
