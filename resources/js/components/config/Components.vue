<template>
    <div>
        <div class="action-components" v-if="actionType">
            <Modal v-if="modal" :modal="modal" :error-code="ui.errorCode" :error-message="ui.errorMessage"
                   :validation-errors="ui.validationErrors" :loading="ui.loading" :clear-error="clearError"
                   :editable="ui.editable" @close-modal="onCloseModel"></Modal>
            <Draggable v-model="components" class="row action-components" group="components"
                       handle=".drag-handle-component" v-bind="dragOptions" v-if="actionType">
                <template v-for="component in components">
                    <div :class="'mb-3 col-' + component.width">
                        <div class="card">
                            <div class="card-header px-2 py-1 d-flex justify-content-between border-primary">
                            <span class="text-primary disable-user-select mouse-pointer flex-grow-1"
                                  @click="onEditComponent(component)">
                                <span class="material-icons">{{
                                        getComponentIcon(component.namespace + '/' + component.identifier)
                                    }}</span>
                                <span>{{ component.label ? component.label + ' - ' : '' }}</span>
                                <span class="text-muted">{{ component.namespace + '/' + component.identifier }}</span>
                            </span>
                                <div class="text-nowrap" v-if="ui.editable">
                                    <button class="btn btn-sm btn-light text-muted p-0 ml-2"
                                            @click="copyComponent(component)">
                                        <span class="material-icons">content_copy</span>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger ml-2 py-0 px-1"
                                            @click="deleteComponent(component.id)">
                                        <span class="material-icons">close</span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body hover-pointer p-2">
                                <!-- Komponenten werden in der config-app.js global registriert -->
                                <component
                                    :is="toComponentName(component.namespace, component.identifier, component.version)"
                                    :component-id="component.id" :component-options="component.options"
                                    :change-component-width="changeComponentWidth"
                                    :update-component-options="updateComponentOptions"
                                    :delete-component="deleteComponent" :width-label="widthLabel"
                                    :copy-element="copyElement" :clear-copied-element="clearCopiedElement"
                                    :copied-element="copiedElement" :loading="ui.loading" :error-code="ui.errorCode"
                                    :error-message="ui.errorMessage" :clear-error="clearError" :set-error="setError"
                                    :validation-errors="ui.validationErrors" :action-type-inputs="actionType.inputs"
                                    :action-type-outputs="actionType.outputs" :definition="definition"
                                    :modal-component="pluginOptionsModal" @report-field-names="updateFieldNames"
                                    :open-modal="onOpenModal" :close-modal="onCloseModel" :editable="ui.editable"/>
                            </div>
                            <div class="card-footer px-2 py-1">
                                <div class="d-flex justify-content-between">
                                    <div class="text-nowrap d-flex">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm p-0 btn-light dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false"
                                                    :disabled="!ui.editable">
                                                <small class="text-muted">{{ widthLabel(component.width) }}</small>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button type="button" class="dropdown-item"
                                                        @click="changeComponentWidth(component.id, 3)">1 / 4
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeComponentWidth(component.id, 4)">1 / 3
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeComponentWidth(component.id, 6)">1 / 2
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeComponentWidth(component.id, 8)">2 / 3
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeComponentWidth(component.id, 9)">3 / 4
                                                </button>
                                                <button type="button" class="dropdown-item"
                                                        @click="changeComponentWidth(component.id,12)">1 / 1
                                                </button>
                                            </div>
                                        </div>
                                        <button
                                            class="btn border-0 btn-sm btn-light p-0 px-1 ml-1 text-muted drag-handle-component"
                                            v-if="ui.editable">
                                            <span class="material-icons text-muted">open_with</span>
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-end text-nowrap">
                                        <button class="btn border-0 btn-sm btn-light p-0 px-1 text-muted"
                                                @click="openComponentDisplayRules(component)">
                                            <span
                                                :class="'material-icons mr-1 ' + (((component.options.display || {}).shown || []).length ? 'text-secondary' : '')">visibility</span>
                                            <span
                                                :class="'material-icons mr-1 ' + (((component.options.display || {}).hidden || []).length ? 'text-secondary' : '')">visibility_off</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </Draggable>
        </div>
        <div class="row" v-if="ui.editable">
            <div class="col">
                <button class="btn btn-sm btn-primary" @click="onAddComponent">Komponente hinzufügen</button>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown"
                     v-if="ui.editable && showPasteComponentButton">
                    <button class="btn btn-sm btn-warning" @click="pasteComponent">
                        Einfügen
                    </button>
                    <button class="btn btn-sm btn-warning" @click="clearCopiedElement">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import {reduxActions} from '../../store/develop-and-config';
import utils from '../../config-utils';
import Modal from '../plugin-utils/Modal';
import Draggable from "vuedraggable";
import Docs from "../utils/Docs";

export default {
    components: {
        Docs,
        Draggable,
        Modal
    },
    props: {
        actionType: Object,
    },
    data() {
        return {
            modal: null,
            inputNamesByComponent: {},
            dragOptions: {
                animation: 0,
                disabled: false,
                ghostClass: "drag-clone"
            },
            pluginOptionsModal: this.$options.components.PluginModal
        };
    },
    computed: {
        ...mapGetters([
            'definition',
            'ui',
        ]),
        allComponentInputNames() {
            return [
                ...new Set([
                    ...Object.values(this.inputNamesByComponent).flat(),
                    ...this.actionType.inputs.map(ele => ele.name),
                    ...this.actionType.outputs.map(ele => ele.name),
                ])
            ].sort();
        },
        components: {
            get() {
                return this.actionType.components;
            },
            set(newComponents) {
                let sorted = [
                    ...newComponents.map((ele, index) => ({
                        ...ele,
                        sort: index
                    }))
                ];

                this.updateActionType({
                    ...this.actionType,
                    components: sorted
                });
            }
        },
        copiedElement() {
            return this.getCopyElement();
        },
        showPasteComponentButton() {
            let componentsNames = [
                'allisa/form',
                'allisa/collection',
                'allisa/progress-bar',
                'allisa/file-preview'
            ];
            let name = this.copiedElement?.name;
            return (componentsNames.indexOf(name) > -1);
        },
        actionTypePlugins() {
            let internalActionTypePlugins = this.ui.plugins.internal.actionTypeComponent;
            let externalActionTypePlugins = this.ui.plugins.external.actionTypeComponent;
            return internalActionTypePlugins.concat(externalActionTypePlugins);
        }
    },
    methods: {
        ...mapActions(reduxActions), ...utils,
        toComponentName(namespace, identifier, version) {
            // Es wird 1.0.0 -> v1_0_0 damit die Version als Verzeichnis-Name genutzt werden kann.
            let versionIdentifier = version.replaceAll('.', '_');

            // In der Konfiguration ist z.B. "progress-bar" eingetragen, doch der Plugin-Name bei der Vue Component Registrierung ist "progressbar".
            let replaceIdentifier = identifier.replaceAll('-', '');

            return namespace + '-actiontypecomponent-' + replaceIdentifier + '-v' + versionIdentifier + '-configuration-template';
        },
        onAddComponent() {
            this.openModal({
                componentName: 'AddComponentModal',
                data: {
                    sort: this.actionType.components.length + 1,
                    actionTypeId: this.actionType.id,
                }
            });
        },
        onEditComponent(component) {
            this.openModal({
                componentName: 'EditComponentModal',
                data: {
                    component: component
                }
            });
        },
        updateComponent(component) {
            return this.patchDefinition('UpdateComponent', component).catch(() => {
            });
        },
        updateComponentOptions(id, options) {
            let component = this.components.find(ele => ele.id === id);

            if (!component) {
                return;
            }

            return this.updateComponent({
                ...component,
                options: options
            });
        },
        updateActionType(actionType) {
            return this.patchDefinition('UpdateActionType', actionType).catch(() => {
            });
        },
        deleteComponent(componentId) {
            let that = this;
            let actionType = {
                ...this.actionType,
                components: [...this.actionType.components].filter(comp => comp.id !== componentId)
            };

            // Komponente löschen und anschließend die inputNamesByComponent (und damit allComponentInputNames) aktualisieren.
            return this.updateActionType(actionType).then(function () {
                let inputNamesByComponent = {...that.inputNamesByComponent};
                delete inputNamesByComponent[componentId];
                that.inputNamesByComponent = inputNamesByComponent;
            });
        },
        changeComponentWidth(componentId, width) {
            let component = this.actionType.components.find(ele => ele.id === componentId);
            let direction = component.width > width ? 'increase' : 'decrease';

            if (!component) {
                return;
            }

            // Ab einer Größe von <= "6", werden Sets automatisch auf "12" gesetzt und alle Felder auf mind. 6.
            if (width === 6 && direction === 'decrease' && component.namespace === 'allisa' && component.identifier === 'form') {
                component = this.setMinSetWidth(component, 12);
                component = this.setMinFieldWidth(component, 6);
            }

            // Bei einer Größe von "3", werden Sets und Felder auf "12" gesetzt.
            // Ab einer Größe von <= "6", werden Sets automatisch auf "12" gesetzt und alle Felder auf mind. 6.
            if (width === 4 && direction === 'decrease' && component.namespace === 'allisa' && component.identifier === 'form') {
                component = this.setMinFieldWidth(component, 12);
            }

            component = {
                ...component,
                width: width
            };

            return this.updateComponent(component);
        },
        setMinSetWidth(component, width) {
            let sets = [...component.options.sets];

            for (let i = 0; i < sets.length; i++) {
                sets[i] = {
                    ...sets[i],
                    width
                };
            }

            return {
                ...component,
                options: {
                    ...component.options,
                    sets
                }
            };
        },
        setMinFieldWidth(component, width) {
            let sets = [...component.options.sets];

            for (let i = 0; i < sets.length; i++) {
                sets[i] = {
                    ...sets[i],
                    fields: sets[i].fields.map(function (field) {
                        if (field.width < width) {
                            return {
                                ...field,
                                width
                            };
                        }

                        return field;
                    })
                };
            }

            return {
                ...component,
                options: {
                    ...component.options,
                    sets
                }
            };
        },
        widthLabel(width) {
            let labels = {
                1: '1/12',
                2: '1/6',
                3: '1/4',
                4: '1/3',
                5: '5/12',
                6: '1/2',
                7: '7/12',
                8: '2/3',
                9: '3/4',
                10: '5/6',
                11: '11/12',
                12: '1/1'
            };

            return labels[width] || '?/?';
        },
        openComponentDisplayRules(component) {
            this.openModal({
                componentName: 'DisplayRulesModal',
                data: {
                    component: {...component},
                    ruleParts: [
                        'shown',
                        'hidden'
                    ],
                    inputNames: this.allComponentInputNames,
                    roles: this.definition.roles,
                }
            });
        }, // Die Komponenten melden beim Mount die Input-Names.
        updateFieldNames(componentId, inputNames) {
            this.inputNamesByComponent = {
                ...this.inputNamesByComponent,
                [componentId]: [...inputNames]
            };
        },
        getComponentIcon(namespace) {
            let plugin = this.actionTypePlugins.find((plugin) => plugin.full_namespace === namespace);
            if (plugin) {
                return plugin.data.icon || 'ballot';
            }
            return 'ballot';
        },
        onOpenModal(options) {
            this.modal = options;
        },
        onCloseModel() {
            $('#genericModal').modal('hide');
            this.modal = null;
        },
        copyElement(name, object) {
            this.saveCopyElement(name, object);
        },
        copyComponent(component) {
            this.saveCopyElement(component.namespace + '/' + component.identifier, component);
        },
        pasteComponent() {
            let data = {
                name: this.copiedElement.name,
                options: {
                    action_type_id: this.actionType.id
                }
            };
            this.patchDefinition('PasteElement', data).catch(() => {
            });
        },
        clearCopiedElement() {
            this.clearCopyElement();
        }
    },
};
</script>

<style scoped>

.dropdown-toggle::after {
    content: none;
}

</style>
