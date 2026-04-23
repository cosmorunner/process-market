<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ModalHeader title="Label bearbeiten"/>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0">Label</label>
                    <input class="form-control" v-model="label" :readonly="!ui.editable"/>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label for="name" class="mb-0">CSS-Klassen</label>
                    <input type="text" class="form-control form-control-sm" id="css_classes" v-model="css_classes" />
                    <small class="text-muted">Mehrere Klassen mit Leerzeichen trennen.</small>
                </div>
            </div>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'" />
        </div>
    </div>
</template>

<script>

import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import utils from "../../config-utils";
import {mapActions, mapGetters} from "vuex";
import {reduxActions} from "../../store/develop-and-config";

export default {
    components: {
        ModalHeader,
        ModalFooter,
    },
    data() {
        return {
            label: '',
            css_classes: ''
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'list_configs'
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let payload = {
                ...this.ui.modal.data.component,
                label: this.label,
                css_classes: this.css_classes
            };

            this.patchDefinition('UpdateComponent', payload).then(this.closeModal).catch(this.closeModal);
        },
    },
    mounted() {
        if (this.ui.modal.data.component) {
            this.label = this.ui.modal.data.component.label;
            this.css_classes = this.ui.modal.data.component.css_classes;
        }
    }
};
</script>
