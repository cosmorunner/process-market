<template>
    <div :class="'mb-3 px-1 col-' + field.width">
        <div class="card h-100 form-field">
            <div class="card-header px-1 py-1 border-info">
                <div class="d-flex justify-content-between">
                    <span class="text-info text-truncate flex-grow-1" @click="openEditModal">
                        <span class="material-icons">{{ iconForType(field.type) }}</span>
                        <span :class="'disable-user-select ' + (hasDuplicateField(field.name) ? 'text-danger' : '')"
                              :title="field.name">{{
                                field.name
                            }}</span>
                    </span>
                    <div class="d-flex justify-content-end">
                        <InputOutputInfo v-if="field.width > 2" :field="field" :action-type-inputs="actionTypeInputs"
                                         :action-type-outputs="actionTypeOutputs"
                                         :process-type-outputs="definition.outputs"/>
                        <button class="btn btn-sm btn-light text-muted p-0 ml-2" @click="copyField">
                            <span class="material-icons">content_copy</span>
                        </button>
                        <button class="btn btn-sm btn-light text-danger p-0 ml-2" @click="deleteField(field.name)"
                                v-if="editable">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body hover-pointer px-1 py-1" style="background-color:#edf6fd;" @click="openEditModal">
                <component :is="toFieldBuilderName(field.type)" :field="field" :action-type-inputs="actionTypeInputs"
                           :action-type-outputs="actionTypeOutputs" :process-type-outputs="definition.outputs"
                           :definition="definition"/>
            </div>
            <div class="card-footer px-1 py-1">
                <div class="d-flex justify-content-between">
                    <div class="text-nowrap d-flex">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm p-0 btn-light dropdown-toggle"
                                    data-toggle="dropdown" aria-expanded="false" :disabled="!editable">
                                <small class="text-muted" v-if="field.type !== 'hidden'">{{
                                        widthLabel(field.width)
                                    }}</small>
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(1)">1 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(2)">1 / 6</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(3)">1 / 4</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(4)">1 / 3</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(5)">5 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(6)">1 / 2</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(7)">7 / 12</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(8)">2 / 3</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(9)">3 / 4</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(10)">5 / 6</button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(11)">11 / 12
                                </button>
                                <button type="button" class="dropdown-item" @click="changeFieldWidth(12)">1 / 1</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end text-nowrap">
                        <button class="btn border-0 btn-sm btn-light p-0 px-1 ml-1 text-muted drag-handle-field badge"
                                v-if="editable">
                            <span
                                :class="'material-icons text-muted ' + (field.width <= 2 ? 'mi-0-75x' : '')">open_with</span>
                        </button>
                        <button class="btn border-0 btn-sm btn-light p-0 px-1 text-muted badge"
                                @click="openDisplayRulesModal(field.name)">
                            <span v-if="hiddenRule.includes(field.type) && field.width > 2"
                                  :class="'material-icons mr-1 ' + (((field.display || {}).shown || []).length ? 'text-secondary' : '')">visibility</span>
                            <span v-if="hiddenRule.includes(field.type) && field.width > 2"
                                  :class="'material-icons mr-1 ' + (((field.display || {}).hidden || []).length ? 'text-secondary' : '')">visibility_off</span>
                            <span v-if="requiredRule.includes(field.type) && field.width > 2"
                                  :class="'material-icons mr-1 ' + (((field.display || {}).required || []).length ? 'text-secondary' : '')">priority_high</span>
                            <span v-if="readonlyRule.includes(field.type) && field.width > 2"
                                  :class="'material-icons mr-1 ' + (((field.display || {}).readonly || []).length ? 'text-secondary' : '')">border_color</span>
                            <!-- Wenn Breite < 2 -->
                            <span v-if="field.width <= 2"
                                  :class="'mi-0-75x material-icons ' + (((field.display || {}).hidden || []).length ? 'text-secondary' : '')">visibility_off</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AlertBuilder from "./builder/AlertBuilder";
import AutocompleteBuilder from "./builder/AutocompleteBuilder";
import BoxBuilder from "./builder/BoxBuilder";
import CheckboxBuilder from "./builder/CheckboxBuilder";
import CurrencyBuilder from "./builder/CurrencyBuilder.vue";
import DatepickerBuilder from "./builder/DatepickerBuilder";
import DisplayRulesModal from "./options/DisplayRulesModal";
import FileBuilder from "./builder/FileBuilder";
import HiddenBuilder from "./builder/HiddenBuilder";
import IconBuilder from "./builder/IconBuilder";
import InputOutputInfo from "./builder/InputOutputInfo";
import LinkBuilder from "./builder/LinkBuilder";
import ListmodalBuilder from "./builder/ListmodalBuilder";
import OptionsModal from "./options/OptionsModal";
import ParagraphBuilder from "./builder/ParagraphBuilder";
import RadioBuilder from "./builder/RadioBuilder";
import SelectBuilder from "./builder/SelectBuilder";
import TextBuilder from "./builder/TextBuilder";
import TextareaBuilder from "./builder/TextareaBuilder";
import TimepickerBuilder from "./builder/TimepickerBuilder";

export default {
    components: {
        InputOutputInfo,
        AlertBuilder,
        AutocompleteBuilder,
        CheckboxBuilder,
        CurrencyBuilder,
        OptionsModal,
        DatepickerBuilder,
        TimepickerBuilder,
        FileBuilder,
        IconBuilder,
        LinkBuilder,
        ListmodalBuilder,
        ParagraphBuilder,
        RadioBuilder,
        SelectBuilder,
        TextBuilder,
        HiddenBuilder,
        TextareaBuilder,
        BoxBuilder,
        DisplayRulesModal
    },
    props: {
        field: Object,
        fieldNames: Array,
        openModal: Function,
        updateField: Function,
        deleteField: Function,
        widthLabel: Function,
        copyElement: Function,
        actionTypeInputs: Array,
        actionTypeOutputs: Array,
        definition: Object,
        editable: Boolean
    },
    data() {
        return {
            shownRule: [
                'alert',
                'autocomplete',
                'checkbox',
                'currency',
                'datepicker',
                'file',
                'icon',
                'link',
                'listmodal',
                'paragraph',
                'radio',
                'select',
                'text',
                'textarea',
                'timepicker',
                'box'
            ],
            hiddenRule: [
                'alert',
                'autocomplete',
                'checkbox',
                'currency',
                'datepicker',
                'file',
                'icon',
                'link',
                'listmodal',
                'paragraph',
                'radio',
                'select',
                'text',
                'textarea',
                'timepicker',
                'box'
            ],
            requiredRule: [
                'autocomplete',
                'checkbox',
                'currency',
                'datepicker',
                'file',
                'listmodal',
                'radio',
                'select',
                'text',
                'textarea',
                'timepicker'
            ],
            readonlyRule: [
                'autocomplete',
                'checkbox',
                'currency',
                'datepicker',
                'file',
                'listmodal',
                'radio',
                'select',
                'text',
                'textarea',
                'timepicker'
            ]
        };
    },
    methods: {
        iconForType(type) {
            return {
                alert: 'announcement',
                autocomplete: 'local_offer',
                checkbox: 'check_box',
                currency: 'euro_symbol',
                datepicker: 'today',
                file: 'attach_file',
                hidden: 'visibility_off',
                icon: 'star',
                link: 'link',
                listmodal: 'chrome_reader_mode',
                paragraph: 'short_text',
                radio: 'radio_button_checked',
                select: 'view_list',
                text: 'create',
                textarea: 'title',
                timepicker: 'schedule',
                box: 'check_box_outline_blank'
            }[type] || 'help';
        },
        toFieldBuilderName(type) {
            return type.charAt(0).toUpperCase() + type.slice(1) + 'Builder';
        },
        changeFieldWidth(width) {
            let field = {
                ...this.field,
                width: width
            };

            this.updateField({
                field: field,
                originalName: field.name
            });
        },
        openEditModal() {
            let inputOutputNames = [
                ...new Set([
                    ...this.actionTypeOutputs.map(ele => ele.name),
                    ...this.definition.outputs.map(ele => ele.name),
                    ...this.actionTypeInputs.map(ele => ele.name),
                    ...this.fieldNames
                ])
            ];

            this.openModal({
                title: 'Feld bearbeiten',
                component: OptionsModal,
                onConfirm: this.updateField,
                data: {
                    field: this.field,
                    originalName: this.field.name,
                    inputOutputNames: inputOutputNames,
                    definition: this.definition
                }
            });
        },
        openDisplayRulesModal(fieldName) {
            let onConfirm = (newRules) => {
                return this.updateField({
                    field: {
                        ...this.field,
                        display: newRules
                    },
                    originalName: this.field.name
                });
            };

            // Die Werte bei "Wählen" setzen sich aus Formularfelder und Aktionstyp-Inputs zusammen.
            let inputNames = [
                ...new Set([
                    ...this.actionTypeInputs.map(ele => ele.name),
                    ...this.actionTypeOutputs.map(ele => ele.name),
                    ...this.fieldNames
                ])
            ].sort();

            this.openModal({
                title: fieldName + ' - Anzeigeregeln',
                component: DisplayRulesModal,
                onConfirm: onConfirm,
                data: {
                    modalTitle: this.field.name,
                    displayRules: this.field.display || {},
                    ruleParts: [
                        'shown',
                        'hidden',
                        'required',
                        'readonly'
                    ].filter(ele => this[ele + 'Rule'].includes(this.field.type)),
                    inputNames: inputNames,
                    roles: this.definition.roles,
                }
            });
        },
        hasDuplicateField(name) {
            return this.fieldNames.filter(ele => ele === name).length > 1;
        },
        copyField() {
            this.copyElement('allisa/form::field', this.field);
        }
    }
};
</script>

<style scoped>
.dropdown-toggle::after {
    content: none;
}
</style>
