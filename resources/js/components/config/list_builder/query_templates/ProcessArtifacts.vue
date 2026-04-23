<template>
    <div class="row">
        <div class="col-12">

            <div class="form-group mb-1">
                <div class="d-flex justify-content-between">
                    <label for="actionsSelection">Aktionen
                        <span v-if="actionsSelect.length" class="badge badge-light">{{ actionsSelect.length }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="actionsSelect = []" v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="actionsSelection" v-model="actionsSelect" size="8"
                        :disabled="!ui.editable">
                    <option v-for="action in definition.action_types" :value="action.id">
                        {{ action.name }}
                    </option>
                </select>
            </div>
            <small class="text-muted">Nur Dokumente aus diesen Aktionen anzeigen. Leer lassen für alle.</small>

            <div class="form-group mt-3 mb-1">
                <div class="d-flex justify-content-between">
                    <label for="actionsDataSelection">Aktions-Daten
                        <span v-if="actionsDataSelect.length" class="badge badge-light">{{
                                actionsDataSelect.length
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="actionsDataSelect = []"
                            v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="actionsDataSelection" v-model="actionsDataSelect" size="8"
                        :disabled="!ui.editable">
                    <option v-for="data in actionsData" :value="data.value">
                        {{ data.label }}
                    </option>
                </select>
            </div>
            <small class="text-muted">Nur Dokumente von diesen Aktions-Datenfeldern. Leer lassen für alle.</small>

            <div class="form-group mt-3 mb-1">
                <div class="d-flex justify-content-between">
                    <label for="documentTypesSelection">Dokumenttypen
                        <span v-if="documentTypesSelect.length" class="badge badge-light">{{
                                documentTypesSelect.length
                            }}</span>
                    </label>
                    <button class="btn btn-sm btn-link pr-1 text-muted" @click="documentTypesSelect = []"
                            v-if="ui.editable">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <select multiple class="form-control p-2" id="documentTypesSelection" v-model="documentTypesSelect"
                        size="8" :disabled="!ui.editable">
                    <option v-for="type in documentTypes" :value="type.value">
                        {{ type.label }}
                    </option>
                </select>
            </div>
            <small class="text-muted">Nur Dokumente von diesen Typen anzeigen. Leer lassen für alle.</small>

            <div class="form-group mt-3 mb-1">
                <div class="custom-control custom-switch mb-1">
                    <input type="checkbox" class="custom-control-input" id="lastDocument" :disabled="!ui.editable"
                           :checked="isLastDocumentChecked" @click="toggleLastDocumentCheck">
                    <label class="custom-control-label" for="lastDocument">Nur aktuellste Versionen anzeigen</label>
                </div>
            </div>
            <small class="text-muted">Nur das zuletzt hochgeladene/erzeugte Dokument pro Aktions-Datenfeld (falls vorhanden) oder Aktionstyp anzeigen.</small>

        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from 'vuex';
import utils from '../../../../config-utils';
import {reduxActions} from '../../../../store/develop-and-config';

export default {
    props: {
        aliasLabels: Object,
        usedColumnAliases: Array | Object,
        supportData: Object | null,
        data: Object
    },
    data() {
        // e.g. whereIn looks like this:
        // [
        //     [
        //        "actions.action_type_id",
        //        [
        //            "b04b1350-62a3-44d3-84f4-5960966099fe",
        //            "5b593d4d-afdc-455f-9c15-2fe20ac1aa41"
        //        ]
        //     ],
        //     [
        //        "artifacts.output",
        //        [
        //           "upload"
        //        ]
        //     ]
        // ]
        let whereIn = this.data.source.whereIn || [];
        let actionsSelect = whereIn.filter(ele => ele[0] === ('actions.action_type_id'));
        let actionsDataSelect = whereIn.filter(ele => ele[0] === ('artifacts.output'));
        let documentTypesSelect = whereIn.filter(ele => ele[0] === ('artifacts.type_type'));

        return {
            actionsSelect: actionsSelect.length ? actionsSelect[0][1] : [],
            actionsDataSelect: actionsDataSelect.length ? actionsDataSelect[0][1] : [],
            documentTypes: this.supportData.documentTypes,
            documentTypesSelect: documentTypesSelect.length ? documentTypesSelect[0][1] : [],
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition'
        ]),
        actionsData() {
            // Get all selected actions data fields of type file.
            return this.definition.action_types.filter(action => this.actionsSelect.includes(action.id))
                .reduce(function (accumulator, action) {
                    action.outputs.filter(field => field.validation.includes('file'))
                        .map(field => accumulator.push({
                            value: field.name,
                            label: field.name + ' - ' + action.name
                        }));
                    return accumulator;
                }, []);
        },
        actionsWhereIn() {
            return this.actionsSelect.length ? [
                'actions.action_type_id',
                [...this.actionsSelect]
            ] : [];
        },
        actionsDataWhereIn() {
            return this.actionsDataSelect.length ? [
                'artifacts.output',
                [...this.actionsDataSelect]
            ] : [];
        },
        documentTypesWhereIn() {
            return this.documentTypesSelect.length ? [
                'artifacts.type_type',
                [...this.documentTypesSelect]
            ] : [];
        },
        isLastDocumentChecked() {
            let where = this.data.source.where || [];
            return !!where.find(element => element[0] === 'artifacts.is_latest');
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        updateListConfigWhereIn() {
            let whereIn = [
                this.actionsWhereIn.length ? this.actionsWhereIn : null,
                this.actionsDataWhereIn.length ? this.actionsDataWhereIn : null,
                this.documentTypesWhereIn.length ? this.documentTypesWhereIn : null,
            ];
            whereIn = whereIn.filter(element => element != null);
            this.$emit('update-source', 'whereIn', whereIn);
        },
        toggleLastDocumentCheck() {
            let where = [...this.data.source.where || []];

            if (this.isLastDocumentChecked) {
                let whereToRemove = ['artifacts.is_latest'];
                where = where.filter(element => !whereToRemove.includes(element[0]));
            }
            else {
                where.push([
                    'artifacts.is_latest',
                    '=',
                    true
                ]);
            }
            this.$emit('update-source', 'where', where);
        }
    },
    watch: {
        actionsSelect() {
            // Remove action data belongs to a deselect action and preserve the previous selection.
            this.actionsDataSelect = this.actionsDataSelect.filter(name => this.actionsData.some(field => field.value === name));
            this.updateListConfigWhereIn();
        },
        actionsDataSelect() {
            this.updateListConfigWhereIn();
        },
        documentTypesSelect() {
            this.updateListConfigWhereIn();
        }
    }
};
</script>
