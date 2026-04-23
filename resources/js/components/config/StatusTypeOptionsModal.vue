<template>
    <div class="modal-dialog modal-lg" role="document" v-if="statusType">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">{{ statusType.name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="dataField">Typ</label>
                    <select class="form-control" id="dataField" @change="onChangePlugin" :value="fullNamespace"
                            :disabled="!ui.editable">
                        <option value="allisa/simple@1.0.0">allisa/simple@1.0.0</option>
                        <option value="allisa/progress@1.0.0">allisa/progress@1.0.0</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="mb-0 d-block">Icon</label>
                    <IconSelection :selected="statusType.image" @on-select-icon="onIconUpdate"/>
                    <div v-for="error in (ui.validationErrors.image || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" :disabled="!ui.editable"
                               :checked="!statusType.hidden ? 'checked' : ''"
                               @click="statusType.hidden = !statusType.hidden"/>
                        <label class="custom-control-label" for="customSwitch1">Anzeigen</label>
                    </div>
                    <small class="text-muted">Versteckte Status/Zustände werden in der Web-Oberfläche der Allisa
                        Plattform nicht angezeigt.</small>
                </div>
                <div class="card rounded-0 border-left-0 border-right-0" v-if="statusType.states.length">
                    <table class="table mb-0" style="font-size: 0.9rem">
                        <tbody>
                        <tr class="d-flex">
                            <td class="col-1 px-2 py-1 border-0"></td>
                            <td class="col-8 px-2 py-1 pr-0 border-0"></td>
                            <td class="col-3 px-2 py-1 pr-0 border-0 text-right">Anzeigen</td>
                        </tr>
                        <template v-for="state in statusType.states">
                            <tr class="d-flex align-items-center">
                                <td class="col-3 px-2 py-1 border-0">
                                    <div class="d-flex flex-row">
                                        <div class="ml-2">
                                            <IconSelection :selected="state.image"
                                                           @on-select-icon="(icon) => onStateIconUpdate(icon, state)"
                                                           :background-color="state.color"
                                                           :dropdown-minimum-width="'46rem'"/>
                                        </div>
                                        <div class="ml-2 mr-2">
                                            <ColorSelection :selected="state.color" v-if="state.image !== ''"
                                                            @on-select-color="(color) => onStateColorUpdate(color, state)"/>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-1 border-0"></td>
                                <td class="col-5 px-2 py-1 pr-0 border-0">
                                    <span>{{ state.description }}</span>
                                    <small class="badge badge-pill badge-light ml-2" v-if="state.min === state.max">{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }}</small>
                                    <small class="badge badge-pill badge-light ml-2" v-else>{{ state.min.endsWith('.000') ? parseInt(state.min) : state.min }} - {{ state.max.endsWith('.000') ? parseInt(state.max) : state.max }}</small>
                                </td>
                                <td class="col-3 px-2 py-1 pr-0 border-0">
                                    <div class="custom-control custom-switch text-right">
                                        <input type="checkbox" class="custom-control-input" :id="state.id"
                                               :checked="(!state.hidden) ? 'checked' : ''" :disabled="!ui.editable"
                                               @click="toggleStateHidden(state.id)"/>
                                        <label class="custom-control-label" :for="state.id"></label>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
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
import StatesTableSimple from "./partials/StatesTableSimple";
import ModalFooter from "./ModalFooter";
import IconSelection from "../utils/IconSelection.vue";
import statusType from "../develop/StatusType.vue";
import ColorSelection from "../develop/ColorSelection.vue";

export default {
    components: {
        ColorSelection,
        IconSelection,
        ModalFooter,
        StatesTableSimple
    },
    data() {
        return {
            statusType: null,
            data: {
                name: '',
                reference: '',
                description: '',
                image: 'settings',
                default: '0',
                smart: []
            },
        };
    },
    computed: {
        ...mapGetters([
            'ui'
        ]),
        fullNamespace() {
            return this.statusType.namespace + '/' + this.statusType.identifier + '@' + this.statusType.version;
        },
        pluginComponentName() {
            return this.toComponentName(this.statusType.namespace, this.statusType.identifier, this.statusType.version);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onChangePlugin(e) {
            let version = e.target.value.split('@')[1];
            let namespaceIdentifier = e.target.value.split('@')[0];
            let namespace = namespaceIdentifier.split('/')[0];
            let identifier = namespaceIdentifier.split('/')[1];

            this.statusType = {
                ...this.statusType,
                namespace,
                identifier,
                version
            };
        },
        onIconUpdate(icon) {
            this.data = {
                ...this.data,
                image: icon
            };
            this.statusType.image = icon;
        },
        onStateIconUpdate(icon, state) {

            if (icon === '') {
                state.color = '';
            }

            const index = this.statusType.states.indexOf(state);
            this.statusType = {
                ...this.statusType,
                states: [
                    ...this.statusType.states.slice(0, index),
                    {
                        ...state,
                        image: icon
                    },
                    ...this.statusType.states.slice(index + 1)
                ]
            };
        },
        onStateColorUpdate(color, state) {
            const index = this.statusType.states.indexOf(state);
            this.statusType = {
                ...this.statusType,
                states: [
                    // everthing before index
                    ...this.statusType.states.slice(0, index),
                    {
                        ...state,
                        color: color
                    },
                    // everthing after index
                    ...this.statusType.states.slice(index + 1)
                ]
            };
        },
        onSave() {
            this.patchDefinition('UpdateStatusType', this.statusType).then(this.closeModal).catch(this.closeModal);
        },
        toComponentName(namespace, identifier, version) {
            let versionIdentifier = version.replaceAll('.', '_');
            return namespace + '-statustype-' + identifier + '-v' + versionIdentifier + '-configuration-template';
        },
        toggleStateHidden(stateId) {
            let states = [...this.statusType.states].map(function (state) {
                if (state.id === stateId) {
                    state = {
                        ...state,
                        hidden: !state.hidden
                    };
                }

                return state;
            });

            this.statusType = {
                ...this.statusType,
                states
            };
        }
    },
    mounted() {
        this.statusType = {...this.ui.modal.data.statusType};
    },
};
</script>
