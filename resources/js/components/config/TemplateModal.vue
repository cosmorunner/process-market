<template>
    <div class="modal-dialog modal-xl modal-100vh" role="document" v-if="template">
        <div class="modal-content" ref="modalContent">
            <div class="modal-header py-2 d-flex align-content-center">
                <h5 class="modal-title">
                    {{ template.id || false ? template.name + ' - Vorlage bearbeiten' : 'Vorlage anlegen' }}</h5>
                <div class="ml-2">
                    <span :class="'d-inline-block p-1 badge badge-' + templateTypeColor(template)">{{templateTypeLabel(template)}}</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="clearError">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <TemplateModalEdit :template="template" @update-property="onUpdateProperty" v-if="!addMode"/>
            <TemplateModalAdd :template="template" @update-property="onUpdateProperty" v-if="addMode"/>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'" @cancel="clearError"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../config-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";

export default {
    components: {
        TemplateModalAdd: () => import("./TemplateModalAdd.vue"),
        TemplateModalEdit: () => import("./TemplateModalEdit.vue"),
        ModalFooter,
    },
    data() {
        return {
            template: null,
            addMode: false,
            loading: false,
            error: null,
            errorMessage: ''
        };
    },
    computed: {
        ...mapGetters(['ui']),
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let that = this;
            let method = this.addMode ? 'StoreTemplate' : 'UpdateTemplate';

            this.patchDefinition(method, this.template).then((response) => {
                if (that.addMode) {
                    that.template = {...response.data.definition.templates.find(ele => ele.name === that.template.name.trim())};
                    that.addMode = false;
                }
                else {
                    that.closeModal();
                }
            }).catch(() => {
            });
        },
        onUpdateProperty(property, value) {
            this.template = {
                ...this.template,
                [property]: value
            };
        },
        templateTypeColor(template) {
            if (template.type === 'html') {
                return 'info';
            }
            if (template.type === 'custom_logic') {
                return 'secondary';
            }
            return 'info';
        },
        templateTypeLabel(template) {
            if (template.type === 'html') {
                return 'HTML';
            }
            if (template.type === 'custom_logic') {
                return 'EIGENE LOGIK';
            }
            return '';
        }
    },
    mounted() {
        if (this.ui.modal.data.template) {
            this.template = {...this.ui.modal.data.template};
        }
        else {
            this.addMode = true;
            this.template = {
                name: '',
                template: ''
            }
        }

        // Mögliche Vorlagen abrufen.
        if (this.addMode) {
            let that = this;
            that.loading = true;
            that.error = false;
            that.errorMessage = null;

            axios.get('/api/templates').then(function (response) {
                that.loading = false;
                that.templates = response.data;
            }).catch(function (error) {
                that.loading = false;
                that.error = true;
                that.errorMessage = error.response.data.message;
            });
        }
    },
    watch: {
        newMapping: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        template: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    }

};
</script>
