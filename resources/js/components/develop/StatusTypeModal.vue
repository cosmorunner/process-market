<template>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <ModalHeader :title="header" docs-article="rules-status" docs-section="create-status"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col">
                        <form>
                            <div class="form-group mb-2">
                                <label for="name" class="mb-0">Name</label>
                                <input type="text" class="form-control form-control-sm" id="name" v-model="data.name"
                                       aria-describedby="name" required>
                                <div v-for="error in (ui.validationErrors.name || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="reference" class="mb-0">Referenz</label>
                                <div class="custom-control custom-switch" v-if="!statusTypeId">
                                    <input type="checkbox" class="custom-control-input" id="autoReference"
                                           :checked="autoReference" @change="autoReference = !autoReference">
                                    <label class="custom-control-label" for="autoReference">Automatische
                                        Referenz</label>
                                </div>
                                <template v-if="!autoReference || statusTypeId">
                                    <input type="text" class="form-control form-control-sm" id="reference"
                                           v-model="data.reference"  aria-describedby="reference" required>
                                    <small class="text-muted">Kleingeschrieben, nur "a-z", "0-9" und "_".</small>
                                </template>
                                <div v-for="error in (ui.validationErrors.reference || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="description" class="mb-0">Beschreibung</label>
                                <textarea class="form-control form-control-sm" v-model="data.description"
                                          id="description" aria-describedby="description"></textarea>
                                <div v-for="error in (ui.validationErrors.description || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <small class="text-muted">{{ data.description.length }} / 200</small>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="mb-0" for="smart">Smart-Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="smart"
                                           :checked="isSmartStatus" @change="toggleSmartStatus">
                                    <label class="custom-control-label" for="smart">&nbsp;</label>
                                </div>
                                <small class="text-muted">Ein Smart-Status berechnet den aktuellen Wert automatisch und
                                    kann nicht durch Statusregeln verändert werden.</small>
                            </div>
                            <div class="form-group mb-2" v-if="!isSmartStatus">
                                <label for="default" class="mb-0">Initial-Wert</label>
                                <input type="text" class="form-control form-control-sm" id="default"
                                       v-model="defaultValue" aria-describedby="default" required>
                                <small class="text-muted">Max. 3 Dezimalstellen. Dezimalstellen mit "." trennen, z.B.
                                    "1.337".</small>
                                <div v-for="error in (ui.validationErrors.default || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <template v-if="isSmartStatus">
                                <div class="form-group mb-2">
                                    <label for="type">Typ</label>
                                    <select class="form-control form-control-sm" id="type"
                                            @change="onTypeChange($event)" :value="data.smart.type">
                                        <option value="relation_type">Verknüpfungstyp</option>
                                        <option value="custom_logic">Eigene Logik</option>
                                        <option value="related_status">Verknüpfter Status</option>
                                    </select>
                                    <div v-for="error in (ui.validationErrors['smart.type'] || [])">
                                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                    </div>
                                </div>
                                <template v-if="data.smart.type === 'relation_type'">
                                    <div class="form-group mb-2">
                                        <AutocompleteSelector
                                            :items="smartRelationTypeValue ? [smartRelationTypeValue] : []"
                                            :label="'Verknüpfungstyp'" :icon="'settings_ethernet'" :max-items="1"
                                            :pipe-include="['relation_types', 'graphs_relation_types']"
                                            @items-changed="onChangeRelationType"/>
                                        <div v-for="error in (ui.validationErrors['smart.options.relation_type'] || [])">
                                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                        </div>
                                    </div>
                                    <span class="d-block mb-2">Konditionen</span>
                                    <template>
                                        <SmartRelationTypeConditionsTable :conditions="smartStatusConditions"
                                                                          @delete-item="onConditionDelete"/>
                                        <SmartRelationTypeConditionsAdd :conditions="smartStatusConditions"
                                                                        @conditions-change="onConditionsChange"/>
                                    </template>
                                    <small class="text-muted">Smart Status bei den Konditionen stehen nicht zur
                                        Verfügung.</small>
                                </template>
                                <template v-if="data.smart.type === 'custom_logic'">
                                    <div class="form-group mb-2">
                                        <label for="template">Eigene-Logik Vorlage</label>
                                        <select class="form-control form-control-sm" id="template"
                                                v-model="data.smart.options.template">
                                            <option value="">Bitte wählen...</option>
                                            <option :value="'template|' + template.id"
                                                    v-for="template in customLogicTemplates">{{ template.name }}
                                            </option>
                                        </select>
                                        <div v-for="error in (ui.validationErrors['smart.options.template'] || [])">
                                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Die "Eigene Logik"-Vorlage muss einen numerischen
                                        Status-Wert zurückgeben, z.B. "1" oder "1,337".</small>
                                </template>
                                <template v-if="data.smart.type === 'related_status'">
                                    <div class="form-group mb-2">
                                        <AutocompleteSelector
                                            :items="smartRelatedStatusValue ? [smartRelatedStatusValue] : []"
                                            :label="'Verknüpfter Prozess'" :icon="'settings_ethernet'" :max-items="1"
                                            :pipe-include="['relation_types', 'graphs_relation_types']"
                                            @items-changed="onChangeRelatedStatus"/>
                                        <label for="status_ref" class="mb-0">Status-Referenz</label>
                                        <input type="text" class="form-control form-control-sm" id="status_ref"
                                               v-model="data.smart.options.status_type_reference"
                                               aria-describedby="name" required>
                                        <div v-for="error in (ui.validationErrors['smart.options.status_type_reference'] || [])">
                                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                        </div>
                                        <label for="default" class="mb-0">Fallback-Wert</label>
                                        <input type="text" class="form-control form-control-sm" id="default"
                                               v-model="data.default" aria-describedby="default" required>
                                        <small class="text-muted">Max. 3 Dezimalstellen. Dezimalstellen mit "." trennen,
                                            z.B.
                                            "1.337".</small>
                                        <div v-for="error in (ui.validationErrors.default || [])">
                                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </form>
                    </div>
                </div>
            </div>
            <ModalFooter :ui="ui" v-on="$listeners" :on-save="onSave" :save-label="title"/>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../develop-utils';
import {reduxActions} from '../../store/develop-and-config';
import ModalFooter from "./ModalFooter";
import ModalHeader from "./ModalHeader";
import IconSelection from "../utils/IconSelection";
import SmartRelationTypeConditionsTable from "./partials/SmartRelationTypeConditionsTable";
import SmartRelationTypeConditionsAdd from "./partials/SmartRelationTypeConditionsAdd";
import SortedRelationTypes from "../utils/SortedRelationTypes";
import AutocompleteSelector from "../utils/AutocompleteSelector";

export default {
    components: {
        AutocompleteSelector,
        SortedRelationTypes,
        SmartRelationTypeConditionsAdd,
        SmartRelationTypeConditionsTable,
        IconSelection,
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            data: {
                name: '',
                reference: '',
                description: '',
                image: 'settings',
                default: '-1',
                smart: [],
            },
            statusTypeId: '',
            autoReference: true
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'process',
            'graphs',
            'status_types',
            'graphs_relation_types',
            'relation_types',
            'templates'
        ]),
        defaultValue: {
            get() {
                return this.data.default.endsWith('.000') ? parseInt(this.data.default) : this.data.default;
            },
            set(val){
                this.data.default = val;
            }
        },
        header() {
            return this.statusType ? 'Status bearbeiten' : 'Status erstellen';
        },
        title() {
            return this.statusType ? 'Speichern' : 'Erstellen';
        },
        statusType() {
            return this.status_types.find(ele => ele.id === this.statusTypeId) || null;
        },
        isSmartStatus() {
            return Object.keys(this.data.smart || {}).length && this.data.smart.type;
        },
        smartStatusConditions() {
            return (this.data.smart || {}).options.conditions || [];
        },
        smartRelationTypeValue() {
            return this.data.smart.options.relation_type || '';
        },
        customLogicTemplates() {
            return this.templates.filter(ele => ele.type === 'custom_logic');
        },
        smartRelatedStatusValue() {
            return this.data.smart.options.relation_type || '';
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let that = this;
            let method = this.statusType ? 'UpdateStatusType' : 'StoreStatusType';
            let position = this.ui.modal.data.position ? JSON.stringify(this.ui.modal.data.position) : null;
            let data = position ? {
                ...this.data,
                position
            } : this.data;

            this.patchDefinition(method, data).then(that.closeModal).catch(() => {
            });
        },
        toggleSmartStatus(e) {
            let isChecked = e.target.checked;

            if (isChecked) {
                this.data = {
                    ...this.data,
                    smart: {
                        type: 'relation_type',
                        options: this.defaultSmartStatusOptions('relation_type')
                    }
                };
            }
            else {
                this.data = {
                    ...this.data,
                    smart: {}
                };
            }
        },
        onTypeChange(e) {
            let type = e.target.value;

            this.data = {
                ...this.data,
                smart: {
                    ...this.data.smart,
                    type: type,
                    options: this.defaultSmartStatusOptions(type)
                }
            };
        },
        onConditionsChange(conditions) {
            this.data = {
                ...this.data,
                smart: {
                    ...this.data.smart,
                    options: {
                        ...this.data.smart.options || {},
                        conditions: [...conditions]
                    }
                }
            };
        },
        onConditionDelete(condition) {
            let conditions = ((this.data.smart || {}).options.conditions || []).filter(ele => {
                return ele.namespace !== condition.namespace || ele.group !== condition.group || ele.status_type_id !== condition.status_type_id || ele.state_id !== condition.state_id || ele.operator !== condition.operator;
            });

            this.data = {
                ...this.data,
                smart: {
                    ...this.data.smart,
                    options: {
                        ...this.data.smart.options || {},
                        conditions: [...conditions]
                    }
                }
            };
        },
        onChangeRelationType(autocompleteItems) {
            if (!autocompleteItems.length) {
                this.data = {
                    ...this.data,
                    smart: {
                        ...this.data.smart,
                        options: {
                            ...this.data.smart.options,
                            relation_type: null,
                            relation_type_name: null,
                        }
                    }
                };

                return;
            }

            let relationType = null;
            let parts = this.getSyntaxParts(autocompleteItems[0]);

            if (parts.namespace) {
                relationType = this.graphs_relation_types.find(ele => ele.reference === parts.key) || null;
            }
            else {
                relationType = this.relation_types.find(ele => ele.reference === parts.key);
            }

            this.data = {
                ...this.data,
                smart: {
                    ...this.data.smart,
                    options: {
                        ...this.data.smart.options,
                        relation_type: relationType ? autocompleteItems[0] : null,
                        relation_type_name: relationType ? relationType.name : null,
                    }
                }
            };
        },
        onChangeRelatedStatus(autocompleteItems) {
            if (!autocompleteItems.length) {
                this.data = {
                    ...this.data,
                    smart: {
                        ...this.data.smart,
                        options: {
                            ...this.data.smart.options,
                            relation_type: null,
                        }
                    }
                };

                return;
            }

            let relationType = null;
            let parts = this.getSyntaxParts(autocompleteItems[0]);

            if (parts.namespace) {
                relationType = this.graphs_relation_types.find(ele => ele.reference === parts.key) || null;
            }
            else {
                relationType = this.relation_types.find(ele => ele.reference === parts.key);
            }

            this.data = {
                ...this.data,
                smart: {
                    ...this.data.smart,
                    options: {
                        ...this.data.smart.options,
                        relation_type: relationType ? autocompleteItems[0] : null,
                    }
                }
            };
        }
    },
    watch: {
        data: {
            handler(data) {
                // When "Smart-Status" is toggled for the first time, we load the process graphs
                if (Object.keys(data.smart).length && !this.graphs.length) {
                    this.fetchUserGraphs();
                }

                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
    },
    mounted() {
        if (this.ui.modal.data.statusTypeId) {
            this.statusTypeId = this.ui.modal.data.statusTypeId;
            this.data = {...this.data, ...this.statusType};

        }
        // Automatically preset name.
        else {
            if(!this.status_types.length) {
                this.data.name = 'Hauptstatus';
            } else {
                this.data.name = 'Status ' + this.status_types.length;
            }
        }
    }
};
</script>
