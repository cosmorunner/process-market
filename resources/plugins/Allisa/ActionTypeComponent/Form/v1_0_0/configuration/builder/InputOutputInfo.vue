<template>
    <div class="text-truncate">
        <span class="badge badge-light text-secondary px-0" v-if="actionInputNames.includes(field.name)"
              data-toggle="tooltip" data-placement="top" title="Vorbelegung durch Aktionstyp-Input.">
            <span class="material-icons">short_text</span>
        </span>
        <span class="badge badge-light text-muted px-0" v-if="!actionInputNames.includes(field.name)"
              data-toggle="tooltip" data-placement="top" title="Keine Vorbelegung durch Aktionstyp-Input.">
            <span class="material-icons">short_text</span>
        </span>
        <span class="badge badge-light text-secondary px-0"
              v-if="actionOutputNames.includes(field.name) && outputTypes.includes(field.type)" data-toggle="tooltip"
              data-placement="top" title="Speicherung in Aktionsdaten.">
            <span class="material-icons">flash_on</span>
        </span>
        <span class="badge badge-light text-muted px-0"
              v-else-if="outputTypes.includes(field.type)" data-toggle="tooltip"
              data-placement="top" title="Keine Speicherung in Aktionsdaten.">
            <span class="material-icons">flash_on</span>
        </span>
        <span class="badge badge-light text-secondary px-0"
              v-if="actionOutputNames.includes(field.name) && processOutputNames.includes(field.name) && outputTypes.includes(field.type)" data-toggle="tooltip"
              data-placement="top" title="Speicherung in Prozessdaten.">
            <span class="material-icons">grain</span>
        </span>
        <span class="badge badge-light text-muted px-0" v-else-if="outputTypes.includes(field.type)" data-toggle="tooltip"
              data-placement="top" title="Keine Speicherung in Prozessdaten.">
            <span class="material-icons">grain</span>
        </span>
    </div>
</template>

<script>
export default {
    props: {
        field: Object,
        actionTypeOutputs: Array,
        actionTypeInputs: Array,
        processTypeOutputs: Array
    },
    data() {
        return {
            actionInputNames: this.actionTypeInputs.map(ele => ele.name),
            actionOutputNames: this.actionTypeOutputs.map(ele => ele.name),
            processOutputNames: this.processTypeOutputs.map(ele => ele.name),
            outputTypes: [
                'autocomplete',
                'checkbox',
                'currency',
                'file',
                'radio',
                'select',
                'textarea',
                'listmodal',
                'text',
                'hidden',
                'datepicker',
                'paragraph',
                'alert'
            ]
        };
    }
};
</script>

