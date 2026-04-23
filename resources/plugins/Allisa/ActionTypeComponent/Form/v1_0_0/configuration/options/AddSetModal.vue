<template>
    <div>
        <div class="form-group input-group-sm mb-2">
            <label class="mb-0">Label</label>
            <input class="form-control" v-model="label" type="text" :readonly="!editable"/>
        </div>
        <div class="form-group mb-2">
            <label for="name" class="mb-0">CSS-Klassen</label>
            <input type="text" class="form-control form-control-sm" id="css_classes" v-model="css_classes" :readonly="!editable"/>
            <small class="text-muted">Mehrere Klassen mit Leerzeichen trennen.</small>
        </div>
    </div>
</template>

<script>

import ModalFooter from "../ModalFooter";
import ModalHeader from "../ModalHeader";

export default {
    components: {
        ModalHeader,
        ModalFooter,
    },
    props: {
        data: Object,
        loading: Boolean,
        validationErrors: Object,
        errorCode: Number | null,
        errorMessage: String,
        editable: Boolean
    },
    data() {
        return {
            label: '',
            css_classes: '',
            sort: this.data.sort,
            width: 12,
            fields: []
        };
    },
    watch: {
        $data: {
            handler: function (data) {
                // Mit "update-parent-data-" werden die Daten an das Eltern-Modal übergeben, wo die Daten abgelegt werden.
                // Wenn dann der Benutzer "Speichern" klickt (onConfirm), werden diese abgelegten Daten an die
                // onConfirm-Methode übergeben.
                this.$emit('update-parent-data', {
                    ...data,
                    label: data.label
                });
            },
            deep: true
        }
    },
    mounted() {
        // Nachdem das Modal initialisiert wurde, müssen wir im Eltern-Modal die Daten ablegen,
        // die gespeichert werden, wenn der Benutzer auf "Speichern" klickt. Diese Daten werden
        // der onConfirm-Methode übergeben.

        this.$emit('update-parent-data', {
            // Hier setzen wir die Daten auf die "set"-Daten, die wir beim Öffnen des Modals übergeben haben (openModal()).
            ...this.$data
        });
    }
};
</script>
