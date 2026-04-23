<template>
    <div class="modal-dialog" role="document" v-if="component.action_type_id">
        <div class="modal-content">
            <ModalHeader title="Neue Komponente"/>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0">Label</label>
                    <input class="form-control" v-model="component.label"/>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label for="name" class="mb-0">CSS-Klassen</label>
                    <input type="text" class="form-control form-control-sm" id="css_classes"
                           v-model="component.css_classes"/>
                    <small class="text-muted">Mehrere Klassen mit Leerzeichen trennen.</small>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0">Typ</label>
                    <select class="form-control" v-model="type">
                        <option value="">Bitte wählen...</option>
                        <option v-for="plugin in actionTypePlugins" :value="plugin.full_namespace">{{
                                plugin.name
                            }}
                        </option>
                    </select>
                </div>
            </div>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'" :save-disabled="!isValid"/>
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
            component: {
                action_type_id: '',
                label: '',
                css_classes: '',
                namespace: '',
                identifier: '',
                version: '1.0.0',
                width: 12,
                options: {}
            }
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'action_types',
            'list_configs'
        ]),
        type: {
            get: function () {
                if (!this.component.namespace) {
                    return '';
                }

                return this.component.namespace + '/' + this.component.identifier;
            },
            set: function (namespace) {
                this.component = {
                    ...this.component,
                    namespace: namespace.split('/')[0],
                    identifier: namespace.split('/')[1],
                    options: this.getDefaultComponentOptions(namespace)
                };
            }
        },
        isValid: function () {
            return this.type !== '';
        },
        actionTypePlugins() {
            let internalActionTypePlugins = this.ui.plugins.internal.actionTypeComponent;
            let externalActionTypePlugins = this.ui.plugins.external.actionTypeComponent;

            return internalActionTypePlugins.concat(externalActionTypePlugins);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            this.patchDefinition('StoreComponent', this.component).then(this.closeModal).catch(this.closeModal);
        },
        getDefaultComponentOptions(namespace) {
            if (namespace === '') {
                return {};
            }
            let plugin = this.actionTypePlugins.find((plugin) => plugin.full_namespace == namespace);
            return plugin.data.default_options || {};
        },
    },
    mounted() {
        if (this.ui.modal.data) {
            this.component.action_type_id = this.ui.modal.data.actionTypeId;
        }
    }
};
</script>
