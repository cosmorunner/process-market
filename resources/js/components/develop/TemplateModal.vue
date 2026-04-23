<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ModalHeader :title="'Titel'"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <p>Body</p>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" :on-save="onSave" :save-label="title"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";

export default {
    components: {
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            data: {},
        };
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
        },
    },
    watch: {
        data: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        title() {
            return this.ui.modal.data ? 'Erstellen' : 'Speichern';
        },
    },
};
</script>
