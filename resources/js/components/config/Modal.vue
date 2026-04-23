<template>
    <div>
        <div class="modal" id="genericModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false"
             aria-labelledby="genericModal" aria-hidden="true">
            <component ref="childModal" v-if="modal" :is="ui.modal.componentName" :data="ui.modal.data"
                       :confirm-label="'Speichern'"></component>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import AddComponentModal from './AddComponentModal';
import EditComponentModal from './EditComponentModal';
import DisplayRulesModal from './DisplayRulesModal';
import EditorProcessorModal from "./processors/EditorProcessorModal";
import EditEnvironmentModal from "./environments/EditEnvironmentModal";
import RelationTypeModal from "./RelationTypeModal";
import EventModal from "./EventModal";
import ListenerModal from "./ListenerModal";
import StatusTypeOptionsModal from "./StatusTypeOptionsModal";
import ConditionsModal from "./processors/ConditionsModal";
import TemplateModal from "./TemplateModal";
import CategoryModal from "./CategoryModal";

export default {
    components: {
        AddComponentModal,
        EditComponentModal,
        ConditionsModal,
        EditorProcessorModal,
        StatusTypeOptionsModal,
        EditEnvironmentModal,
        DisplayRulesModal,
        TemplateModal,
        CategoryModal,
        RelationTypeModal,
        EventModal,
        ListenerModal
    },
    data() {
        return {
            modal: null
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
    },
    methods: {
        ...mapActions(reduxActions)
    },
    created() {
        let that = this;
        $(document).one('hidden.bs.modal', '#genericModal', that.closeModal);
    },
    mounted() {
        let $modal = $('#genericModal');
        $modal.modal('show');
        this.clearError();
        this.modal = this.ui.modal;

        this.setModalClientHeight($modal.get(0).clientHeight)

    },
    beforeDestroy() {
        // Before das generische Modal geschlossen wird, erhält das Child-Modal
        // eine letzte Chance darauf zu reagieren.
        if (this.$refs.childModal.hasOwnProperty('onParentModalClose')) {
            this.$refs.childModal.onParentModalClose();
        }

        $('#genericModal').modal('hide');
    }
};
</script>
