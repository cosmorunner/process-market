<template>
    <div class="modal-dialog modal-lg" role="document" v-if="relationType">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">{{ addMode ? 'Verknüpfungstyp anlegen' : 'Verknüpfungstyp bearbeiten' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="relationType.name"
                           maxlength="80" :readonly="!ui.editable"/>
                    <div v-for="error in (ui.validationErrors.name || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Beschreibung</label>
                    <textarea id="description" class="form-control" name="description" maxlength="200"
                              :readonly="!ui.editable"
                              v-model="relationType.description">{{relationType.description}}</textarea>
                    <div v-for="error in (ui.validationErrors.description || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="connection_type">Verbindungstyp</label>
                    <select class="custom-select custom-select-sm" name="connection_type" :disabled="!ui.editable"
                            v-model="relationType.connection_type">
                        <option value="n-n">N:M</option>
                        <option value="n-1">N:1</option>
                        <option value="1-1">1:1</option>
                        <option value="1-n">1:N</option>
                    </select>
                    <div v-for="error in (ui.validationErrors.connection_type || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                    <small class="text-muted">{{ connectionTypeDescription }}</small>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="reference">Referenz</label>
                    <input type="text" class="form-control" id="reference" name="reference"
                           v-model="relationType.reference" maxlength="80" :readonly="!ui.editable"/>
                    <div v-for="error in (ui.validationErrors.reference || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                    <small class="text-muted">Eindeutige Kennung des Verknüpfungstyps.</small>
                </div>
                <hr/>
                <div>
                    <label class="mb-0">Verknüpfungsdaten</label>
                    <table class="table table-sm" v-if="Object.keys(relationType.default || {}).length">
                        <tbody>
                        <tr v-for="(syntaxValue, key) in (relationType.default || {})" class="d-flex">
                            <td class="col-5">{{ key }}</td>
                            <td class="col-5">
                                <OptionBadges :value="syntaxValue"/>
                            </td>
                            <td class="col-2">
                                <button class="float-right btn btn-sm btn-light" @click="onDeleteData(key)"
                                        v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row" v-if="ui.editable">
                        <div class="col-4">
                            <input type="text" v-model="newKey" class="form-control form-control-sm"
                                   placeholder="Name...(Nur a-z, 0-9, Unterstrich)"/>
                        </div>
                        <div class="col-6">
                            <AutocompleteSelector :items="newValue ? [newValue] : []"
                                                  :add-only-from-autocomplete="false" :max-items="1"
                                                  :syntax-include="Object.keys(syntaxLoaderLabels()).filter(ele => ele !== 'action.outputs')"
                                                  @items-changed="onNewValueChanged"/>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-outline-success" @click="onAddData"
                                    :disabled="!newKey.trim()">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                    <small class="d-block text-muted mb-2">Verknüpfungs-Datensatz ← Wert. Kleingeschrieben, nur a-z, 0-9
                        und "_".</small>
                </div>
            </div>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import utils from '../../config-utils';
import {reduxActions} from "../../store/develop-and-config";
import OptionBadges from "../utils/OptionBadges";
import AutocompleteSelector from "../utils/AutocompleteSelector";
import ModalFooter from "./ModalFooter";

export default {
    components: {
        ModalFooter,
        AutocompleteSelector,
        OptionBadges
    },
    data() {
        return {
            relationType: null,
            addMode: false,
            newKey: '',
            newValue: ''
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
            'relation_types_with_single_process'
        ]),
        connectionTypeDescription() {
            let description = '';
            switch (this.relationType.connection_type) {
                case '1-1':
                    description = 'Die Prozess-Instanz, zu dem dieser Verknupfungstyp gehört, kann eine Prozess-Instanz mit diesem Typ verknüpfen. Der Ziel-Prozess kann nur eine Prozess-Instanz von diesem Typ verknüpfen.';
                    break;
                case '1-n':
                    description = 'Die Prozess-Instanz, zu dem dieser Verknupfungstyp gehört, kann beliebig viele Prozess-Instanzen mit diesem Typ verknüpfen. Die Ziel-Prozesse können nur eine Prozess-Instanz von diesem Typ verknüpfen.';
                    break;
                case 'n-1':
                    description = 'Die Prozess-Instanz, zu dem dieser Verknupfungstyp gehört, kann eine Prozess-Instanz mit diesem Typ verknüpfen. Der Ziel-Prozess kann beliebig viele Prozess-Instanzen von diesem Typ verknüpfen.';
                    break;
                case 'n-n':
                    description = 'Die Prozess-Instanz, zu dem dieser Verknupfungstyp gehört, kann beliebig viele Prozess-Instanzen mit diesem Typ verknüpfen. Die Ziel-Prozesse können beliebig viele Prozess-Instanzen von diesem Typ verknüpfen.';
                    break;
                default:
                    description = '';
            }
            return description;
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreRelationType' : 'UpdateRelationType';
            this.patchDefinition(method, this.relationType).then(this.closeModal).catch(() => {
            });
        },
        onAddData() {
            if (!/^[a-z0-9_]+$/.test(this.newKey)) {
                return;
            }

            this.relationType = {
                ...this.relationType,
                default: {
                    ...this.relationType.default,
                    [this.newKey]: this.newValue
                }
            };

            this.newKey = '';
            this.newValue = '';
        },
        onDeleteData(mappingKey) {
            let defaultData = {...this.relationType.default};

            delete defaultData[mappingKey];

            this.relationType.default = defaultData;
        },
        onNewValueChanged(autoCompleteItems) {
            this.newValue = autoCompleteItems.length ? autoCompleteItems[0] : '';
        }
    },
    watch: {
        relationType: {
            handler() {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    mounted() {
        if (this.ui.modal.data.relationType) {
            this.relationType = {...this.ui.modal.data.relationType};
        }
        else {
            this.addMode = true;
            this.relationType = {
                name: '',
                description: '',
                single: false,
                reference: null,
                default: {}
            };
        }
    },

};
</script>
