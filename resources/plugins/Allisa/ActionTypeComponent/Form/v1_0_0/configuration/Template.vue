<template>
    <div>
        <Draggable v-model="sets" class="row mx-n1" group="sets" handle=".drag-handle-set" v-bind="dragOptions">
            <template v-for="(set, index) in sets">
                <Set :set="set" :set-index="index" :field-names="fieldNames" :action-type-inputs="actionTypeInputs"
                     :action-type-outputs="actionTypeOutputs" :definition="definition" :width-label="widthLabel"
                     :copy-element="copyElement" :clear-copied-element="clearCopiedElement"
                     :copied-element="copiedElement" :open-modal="openModal" :update-set="updateSet"
                     :delete-set="deleteSet" :set-error="setError" :editable="editable"/>
            </template>
        </Draggable>
        <div class="row" v-if="editable">
            <div class="col">
                <button class="btn btn-sm btn-outline-secondary" @click="openAddSetModal">
                    <span class="material-icons">add</span> Set
                </button>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown"
                     v-if="editable && showPasteSetButton">
                    <button class="btn btn-sm btn-warning" @click="pasteSet">
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

import AddSetModal from "./options/AddSetModal";
import EditSetModal from "./options/EditSetModal";
import Draggable from "vuedraggable";
import Set from "./Set";

export default {
    components: {
        Set,
        Draggable
    },
    props: {
        componentId: String,
        componentOptions: Object,
        changeComponentWidth: Function,
        updateComponentOptions: Function,
        deleteComponent: Function,
        widthLabel: Function,
        copyElement: Function,
        clearCopiedElement: Function,
        loading: Boolean,
        errorCode: Number | null,
        errorMessage: String | null,
        clearError: Function,
        setError: Function,
        openModal: Function,
        closeModal: Function,
        validationErrors: Object | Array,
        actionTypeInputs: Array,
        actionTypeOutputs: Array,
        definition: Object,
        modalComponent: Object,
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
        sets: {
            get() {
                return this.componentOptions.sets;
            },
            set(newSets) {
                let sorted = [
                    ...newSets.map((ele, index) => ({
                        ...ele,
                        sort: index
                    }))
                ];

                this.updateComponentOptions(this.componentId, {
                    ...this.componentOptions,
                    sets: sorted
                });
            }
        },
        fieldNames() {
            return this.sets.reduce((total, current) => [
                ...total,
                ...current.fields
            ], []).map(ele => ele.name);
        },
        showPasteSetButton() {
            return this.copiedElement?.name === 'allisa/form::set';
        }
    },
    methods: {
        toFieldBuilderName(type) {
            return type.charAt(0).toUpperCase() + type.slice(1) + 'Builder';
        },
        openAddSetModal() {
            this.openModal({
                title: 'Set hinzufügen',
                component: AddSetModal,
                onConfirm: this.addSet,
                data: {sort: this.componentOptions.sets.length + 1}
            });
        },
        openEditSetModal(set) {
            this.openModal({
                title: 'Set bearbeiten',
                component: EditSetModal,
                onConfirm: this.updateSet,
                data: {set: {...set}}
            });
        },
        addSet(set) {
            let payload = {
                ...this.componentOptions,
                sets: [
                    ...this.componentOptions.sets,
                    set
                ]
            };

            return this.updateComponentOptions(this.componentId, payload).then(this.closeModal);
        },

        /**
         * Aktualisiert ein Set.
         * @param set Daten-Übergabe in EditSetModal an das Eltern-Modal.
         * @returns {*}
         */
        updateSet(set) {
            let that = this;
            let payload = {
                ...this.componentOptions,
                sets: [...this.componentOptions.sets.map(ele => ele.sort === set.sort ? set : ele)]
            };

            return this.updateComponentOptions(this.componentId, payload).then(function () {
                that.$emit('report-field-names', that.componentId, that.fieldNames);
                that.closeModal();
            });
        },
        deleteSet(setIndex) {
            let payload = {
                ...this.componentOptions,
                sets: [...this.sets].filter((ele, index) => index !== setIndex)
            };

            return this.updateComponentOptions(this.componentId, payload);
        },
        pasteSet() {
            let set = this.copiedElement?.object;
            if (set) {
                set.label = set.label ? set.label + ' kopie' : '';
                set.fields.forEach(function (field) {
                    field.name = field.name + '_kopie_' + (Math.floor(Math.random() * 898) + 101);
                });

                set.sort = !this.sets.length ? 0 : Math.max(...this.sets.map(ele => ele.sort)) + 1

                this.addSet(set);
                this.clearCopiedElement();
            }
        }
    },
    mounted() {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Dem Parent mitteilen, welche inputNames es gibt, um diese z.B. bei den DisplayRules nutzen zu können.
        this.$emit('report-field-names', this.componentId, this.fieldNames);
    }
};
</script>

<style>
/*noinspection CssUnusedSymbol*/
.drag-clone {
    opacity: 0.7;
}
</style>
