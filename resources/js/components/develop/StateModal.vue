<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ModalHeader :title="`Zustand für ${statusType.name}`" docs-article="rules-status"
                         docs-section="create-state"/>
            <div class="modal-body py-2">
                <div class="row d-flex">
                    <div class="col-12">
                        <span class="text-muted d-block mb-2" v-if="!isSmartStatusType && data.hasCustomValue">Statustyp-Initialwert: {{
                                statusType.default
                            }}</span>
                        <div class="mb-2" v-if="statusType && statusType.states.length">
                            <div class="accordion" id="accordionExample">
                                <div class="card border-0">
                                    <div class="card-header bg-white p-0" id="headingOne">
                                        <span class="mb-0">
                                            <button class="btn btn-link btn-block text-left px-0" type="button"
                                                    data-toggle="collapse" data-target="#collapseOne"
                                                    aria-expanded="false" aria-controls="collapseOne">
                                                Zustände anzeigen
                                            </button>
                                        </span>
                                    </div>

                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                         data-parent="#accordionExample">
                                        <div class="card-body p-0">
                                            <StatesTableSimple :states="statusType.states"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form>
                            <div class="form-group mb-3">
                                <label for="description" class="mb-0">Beschreibung</label>
                                <input type="text" class="form-control form-control-sm" id="description"
                                       v-model="data.description" aria-describedby="description" required>
                                <div v-for="error in (ui.validationErrors.description || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="form-group custom-control custom-switch d-inline-block mr-3"
                                     v-if="!editMode">
                                    <input type="checkbox" class="custom-control-input" id="has_custom_value"
                                           :checked="hasCustomValue" @change="toggleValueEdit">
                                    <label class="custom-control-label" for="has_custom_value">Wert manuell
                                        setzen</label>
                                </div>
                                <div class="form-group custom-control custom-switch d-inline-block">
                                    <input type="checkbox" class="custom-control-input" id="is_value_range"
                                           :checked="hasValueRange" @change="toggleValueRange">
                                    <label class="custom-control-label" for="is_value_range">Wertebereich</label>
                                </div>
                            </div>
                            <div id="collapseOne2">
                                <div class="form-group mb-2" v-if="editMode || hasCustomValue">
                                    <label for="min" class="mb-0">{{ hasValueRange ? "Min-Wert" : "Wert" }}</label>
                                    <input type="text" class="form-control form-control-sm" id="min" v-model="minValue"
                                           aria-describedby="min" required>
                                    <small class="text-muted">Max. 3 Dezimalstellen. Dezimalstellen mit "." trennen,
                                        z.B.
                                        "1.575".</small>
                                    <div v-for="error in (ui.validationErrors.min || [])">
                                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                    </div>
                                </div>
                                <div v-bind:class="hasValueRange ? '' : 'd-none'">
                                    <div id="collapseOne3" class="form-group mb-2">
                                        <label for="max" class="mb-0">Max-Wert</label>
                                        <input type="text" class="form-control form-control-sm" id="max"
                                               v-model="maxValue" aria-describedby="max" required>
                                        <div v-for="error in (ui.validationErrors.max || [])">
                                            <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
import ColorSelection from "./ColorSelection";
import StatesTableSimple from "./StatesTableSimple";

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        StatesTableSimple,
        ColorSelection,
        IconSelection,
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            data: {
                id: '',
                description: '',
                min: '',
                max: '',
                image: 'arrow_forward',
                color: '#72c6ff'
            },
            hasCustomValue: false,
            hasValueRange: false,
            stateId: '',
            editMode: false,
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'status_types'
        ]),
        minValue: {
            get() {
                return this.data.min.endsWith('.000') ? parseInt(this.data.min) : this.data.min;
            },
            set(val) {
                this.data.min = val;
            }
        },
        maxValue: {
            get() {
                return this.data.max.endsWith('.000') ? parseInt(this.data.max) : this.data.max;
            },
            set(val) {
                this.data.max = val;
            }
        },
        title() {
            return this.state ? 'Speichern' : 'Erstellen';
        },
        state() {
            return this.statusType ? this.statusType.states.find(ele => ele.id === this.stateId) : null;
        },
        statusType() {
            return this.status_types.find(ele => ele.id === this.ui.modal.data.statusTypeId);
        },
        isSmartStatusType() {
            if (this.statusType) {
                return Object.keys(this.statusType.smart || {}).length > 0;
            }

            return false;
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let method = this.stateId ? 'UpdateState' : 'StoreState';
            let position = this.ui.modal.data.position ? JSON.stringify(this.ui.modal.data.position) : null;

            if (!this.data.min?.trim()) {
                delete this.data.min;
            }

            if (!this.data.max?.trim()) {
                delete this.data.max;
            }

            let data = {
                ...this.data,
                status_type_id: this.statusType.id
            };

            if (position) {
                data.position = position;
            }

            this.patchDefinition(method, data).then(this.closeModal);
        },
        toggleValueEdit() {
            this.hasCustomValue = !this.hasCustomValue;
            this.data.min = '';
            this.data.max = '';

            if (!this.hasCustomValue) {
                this.hasValueRange = false;
            }
        },
        toggleValueRange() {

            this.hasValueRange = !this.hasValueRange;
            if (this.hasValueRange) {
                this.hasCustomValue = true;
            }
            this.data.max = '';
        }
    },
    watch: {
        data: {
            handler: function () {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        },
        hasValueRange: function (has) {
            if (!has) {
                this.data.max = '';
            }
        }
    },
    mounted() {
        if (this.ui.modal.data.stateId) {
            this.stateId = this.ui.modal.data.stateId;
            this.data = {...this.state};
            this.hasValueRange = this.data.min !== this.data.max;
        }

        if (!this.hasValueRange) {
            this.data.max = '';
        }

        // Bei der Neuerstellung eines Zustandes
        if (!this.stateId) {
            this.data.description = 'Zustand ' + (this.statusType.states.length + 1);
        }
        else {
            this.editMode = true;
            this.data.hasCustomValue = true;
        }

    }
};
</script>
