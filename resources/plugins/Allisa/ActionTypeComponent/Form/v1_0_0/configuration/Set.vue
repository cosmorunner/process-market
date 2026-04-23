<template>
    <div :class="'mb-3 px-1 col-' + set.width">
        <div class="card form-set mb-0">
            <div class="card-header px-2 py-1 d-flex justify-content-between border-secondary">
                <span class="text-secondary disable-user-select flex-grow-1" @click="openEditModal">
                    <span>{{ set.label }}</span>
                    <span v-if="multipledEnabled">
                        <span v-if="set.label" class="text-muted"> - </span>
                        <span class="text-muted">Min. {{ set.multiple.min }} - Max. {{
                                set.multiple.max
                            }}</span>
                    </span>
                </span>
                <div class="text-nowrap" v-if="editable">
                    <button class="btn btn-sm btn-light text-muted p-0 ml-2" @click="copySet">
                        <span class="material-icons">content_copy</span>
                    </button>
                    <button class="btn btn-sm btn-light text-danger p-0" @click="deleteSet(setIndex)">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>
            <div :class="'card-body hover-pointer p-2 ' + (multipledEnabled ? 'set-bg-striped' : 'set-bg')">
                <Draggable v-model="fields" :group="'fields-' + set.sort" class="row mx-n1" handle=".drag-handle-field"
                           v-bind="dragOptions">
                    <template v-for="field in fields">
                        <Field :field="field" :field-names="fieldNames" :action-type-inputs="actionTypeInputs"
                               :action-type-outputs="actionTypeOutputs" :definition="definition"
                               :width-label="widthLabel" :copy-element="copyElement" :open-modal="openModal"
                               :update-field="updateField" :delete-field="deleteField" :editable="editable"/>
                    </template>
                </Draggable>
                <div class="row" v-if="editable">
                    <div class="col">
                        <button class="btn btn-sm btn-outline-info" @click="openAddFieldModal">
                            <span class="material-icons">add</span> Feld
                        </button>
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown" v-if="editable && showPasteFieldButton">
                            <button class="btn btn-sm btn-warning" @click="pasteField">Einfügen</button>
                            <button class="btn btn-sm btn-warning" @click="clearCopiedElement">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer px-2 py-1">
                <div class="d-flex justify-content-between">
                    <div class="text-nowrap d-flex">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm p-0 btn-light dropdown-toggle"
                                    data-toggle="dropdown" aria-expanded="false" :disabled="!editable">
                                <small class="text-muted">{{ widthLabel(set.width) }}</small>
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="changeSetWidth(1)">1 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(2)">1 / 6</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(3)">1 / 4</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(4)">1 / 3</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(5)">5 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(6)">1 / 2</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(7)">7 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(8)">2 / 3</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(9)">3 / 4</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(10)">5 / 6</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(11)">11 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeSetWidth(12)">1 / 1</button>
                            </div>
                        </div>
                        <button class="btn border-0 btn-sm btn-light p-0 px-1 ml-1 text-muted drag-handle-set"
                                v-if="editable">
                            <span class="material-icons text-muted">open_with</span>
                        </button>
                    </div>
                    <div class="d-flex justify-content-end text-nowrap">
                        <button class="btn border-0 btn-sm btn-light p-0 px-1 text-muted"
                                @click="openDisplayRulesModal">
                            <span
                                :class="'material-icons mr-1 ' + (((set.display || {}).shown || []).length ? 'text-secondary' : '')">visibility</span>
                            <span
                                :class="'material-icons mr-1 ' + (((set.display || {}).hidden || []).length ? 'text-secondary' : '')">visibility_off</span>
                            <span
                                :class="'material-icons mr-1 ' + (((set.display || {}).required || []).length ? 'text-secondary' : '')">priority_high</span>
                            <span
                                :class="'material-icons mr-1 ' + (((set.display || {}).readonly || []).length ? 'text-secondary' : '')">border_color</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import EditSetModal from "./options/EditSetModal";
import DisplayRulesModal from "./options/DisplayRulesModal";
import OptionsModal from "./options/OptionsModal";
import Draggable from "vuedraggable";
import Field from "./Field";
import AddFieldModal from "./options/AddFieldModal";

export default {
    components: {
        EditSetModal,
        DisplayRulesModal,
        OptionsModal,
        Field,
        Draggable
    },
    props: {
        fieldNames: Array,
        set: Object,
        setIndex: Number,
        openModal: Function,
        updateSet: Function,
        deleteSet: Function,
        widthLabel: Function,
        setError: Function,
        copyElement: Function,
        clearCopiedElement: Function,
        actionTypeInputs: Array,
        actionTypeOutputs: Array,
        definition: Object,
        syntaxLoaderValues: Object,
        copiedElement: Object | Array,
        editable: Boolean
    },
    data() {
        return {
            dragOptions: {
                animation: 0,
                disabled: false,
                ghostClass: "drag-clone"
            }
        };
    },
    computed: {
        fields: {
            get() {
                return this.set.fields;
            },
            set(newSorted) {
                this.updateSet({
                    ...this.set,
                    fields: newSorted
                });
            }
        },
        multipledEnabled() {
            return (this.set.multiple || {}).enabled || false;
        },
        showPasteFieldButton() {
            return this.copiedElement?.name === 'allisa/form::field';
        }
    },
    methods: {
        changeSetWidth(width) {
            let set = {
                ...this.set,
                width: width
            };

            return this.updateSet(set);
        },
        updateField(payload) {
            let field = payload.field;
            let originalName = payload.originalName;

            let set = {
                ...this.set,
                fields: this.fields.map(function (oldField) {
                    if (oldField.name === originalName) {
                        return {...field};
                    }

                    return oldField;
                })
            };

            return this.updateSet(set);
        },
        addField(field) {
            let set = {
                ...this.set,
                fields: [
                    ...this.set.fields,
                    field
                ]
            };

            return this.updateSet(set);
        },
        deleteField(name) {
            let set = {
                ...this.set,
                fields: this.set.fields.filter(ele => ele.name !== name)
            };

            return this.updateSet(set);
        },
        openEditModal() {
            this.openModal({
                title: 'Set bearbeiten',
                component: EditSetModal,
                onConfirm: this.updateSet,
                data: {set: this.set}
            });
        },
        openDisplayRulesModal() {
            let onConfirm = (newRules) => {
                return this.updateSet({
                    ...this.set,
                    display: newRules
                });
            };

            let inputNames = [
                ...new Set([
                    ...this.fieldNames,
                    ...this.actionTypeInputs.map(ele => ele.name),
                    ...this.actionTypeOutputs.map(ele => ele.name),
                ])
            ].sort();

            this.openModal({
                title: 'Anzeige-Regeln',
                component: DisplayRulesModal,
                onConfirm: onConfirm,
                data: {
                    displayRules: this.set.display || {},
                    inputNames: inputNames,
                    roles: this.definition.roles,
                }
            });
        },
        openAddFieldModal() {
            let inputOutputNames = [
                ...new Set([
                    ...this.actionTypeOutputs.map(ele => ele.name),
                    ...this.actionTypeInputs.map(ele => ele.name),
                    ...this.definition.outputs.map(ele => ele.name)
                ])
            ].sort();

            this.openModal({
                title: 'Feld hinzufügen',
                component: AddFieldModal,
                onConfirm: this.addField,
                data: {
                    inputOutputNames: inputOutputNames,
                    definition: this.definition,
                    set: this.set
                }
            });
        },
        pasteField() {
            let field = this.copiedElement?.object;
            if (field) {
                field.name = field.name + '_kopie_' + (Math.floor(Math.random() * 898) + 101);
                this.addField(field);
                this.clearCopiedElement();
            }
        },
        copySet() {
            this.copyElement('allisa/form::set', this.set);
        }
    }
};
</script>

<style scoped>

.dropdown-toggle::after {
    content: none;
}

/*noinspection ALL*/
.set-bg {
    background-color: #fdf8ec;
}

/*noinspection ALL*/
.set-bg-striped {
    background: repeating-linear-gradient(
        45deg,
        #fff5e9,
        #fff5e9 20px,
        #ffffff 20px,
        #ffffff 40px
    );
}
</style>
