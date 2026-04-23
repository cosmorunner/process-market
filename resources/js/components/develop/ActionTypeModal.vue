<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ModalHeader :title="header" docs-article="rules-actions" docs-section="create-action"/>
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
                                <div class="custom-control custom-switch" v-if="!actionTypeId">
                                    <input type="checkbox" class="custom-control-input" id="autoReference"
                                           :checked="autoReference" @change="autoReference = !autoReference">
                                    <label class="custom-control-label" for="autoReference">Automatische Referenz</label>
                                </div>
                                <template v-if="!autoReference || actionTypeId">
                                    <input type="text" class="form-control form-control-sm" id="reference"
                                           v-model="data.reference" aria-describedby="reference" required>
                                    <small class="text-muted">Kleingeschrieben, nur "a-z", "0-9" und "_".</small>
                                </template>

                                <div v-for="error in (ui.validationErrors.reference || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="description" class="mb-0">Beschreibung</label>
                                <textarea class="form-control form-control-sm" maxlength="200"
                                          v-model="data.description" id="description"
                                          aria-describedby="description"></textarea>
                                <div v-for="error in (ui.validationErrors.description || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <small class="text-muted">{{ data.description.length }} / 200</small>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="category">Kategorie</label>
                                <select class="form-control form-control-sm" id="category" v-model="data.category_id">
                                    <option :value="category.id" v-for="category in this.categories">{{ category.name }}
                                    </option>
                                </select>
                                <div v-for="error in (ui.validationErrors.category_id || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="mb-0 d-block">Icon</label>
                                <IconSelection :selected="data.image" @on-select-icon="onIconUpdate"/>
                                <div v-for="error in (ui.validationErrors.image || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="mb-0" for="show_save_button">Ausführen-Button verstecken</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="show_save_button"
                                           :checked="!data.show_save_button"
                                           @change="data.show_save_button = !data.show_save_button">
                                    <label class="custom-control-label" for="show_save_button">&nbsp;</label>
                                </div>
                                <small class="text-muted">Bei aktivierter Option wird der Ausführen-Button in der
                                    Weboberläche nicht
                                    angezeigt.</small>
                            </div>
                            <div class="form-group mb-2" v-if="data.show_save_button">

                                <label for="category">Ausführen-Label</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customLabel"
                                           :checked="showOtherRunButtonLabel" @change="onChangeCustomLabel">
                                    <label class="custom-control-label" for="customLabel">
                                        <small>Eigenes Label</small>
                                    </label>
                                </div>
                                <select class="form-control form-control-sm mt-1" id="save_label" v-model="data.save_label" @change="resetOtherRunButtonLabel" v-if="!showOtherRunButtonLabel">
                                    <option value="app.execute">Ausführen</option>
                                    <option value="app.save">Speichern</option>
                                    <option value="app.start">Starten</option>
                                    <option value="app.submit">Absenden</option>
                                    <option value="app.apply">Übernehmen</option>
                                    <option value="app.calculate">Berechnen</option>
                                    <option value="app.create">Erstellen</option>
                                    <option value="app.lay_on">Anlegen</option>
                                    <option value="app.update">Aktualisieren</option>
                                    <option value="app.overwrite">Überschreiben</option>
                                    <option value="app.reset">Zurücksetzen</option>
                                    <option value="app.undo">Rückgängig machen</option>
                                    <option value="app.next_step">Nächster Schritt</option>
                                    <option value="app.save_draft">Entwurf speichern</option>
                                    <option value="app.continue">Weiter</option>
                                    <option value="app.upload">Hochladen</option>
                                    <option value="app.finish">Beenden</option>
                                    <option value="app.stop">Stoppen</option>
                                    <option value="app.delete">Löschen</option>
                                </select>
                                <div v-if="showOtherRunButtonLabel" class="mt-1">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="Speicherbutton-Label..." v-model="otherRunButtonLabel"
                                           @input="setRunButtonLabel" aria-describedby="Speicherbutton-Label">
                                </div>
                                <div v-for="error in (ui.validationErrors.save_label || [])">
                                    <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                                </div>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <label class="mb-0" for="instant">Instant-Aktion</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="instant"
                                           :checked="data.instant" @change="data.instant = !data.instant">
                                    <label class="custom-control-label" for="instant">&nbsp;</label>
                                </div>
                                <small class="text-muted">Beim Klick auf die Aktion in der Web-Oberfläche wird die
                                    Aktion direkt ausgeführt.</small>
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

// noinspection JSUnusedLocalSymbols
export default {
    components: {
        IconSelection,
        ModalHeader,
        ModalFooter
    },
    data() {
        return {
            data: {
                id: '',
                name: '',
                reference: '',
                description: '',
                image: '',
                category_id: '',
                instant: false,
                show_save_button: true,
                save_label: '',
                full_width: false,
            },
            autoReference: true,
            actionTypeId: '',
            otherRunButtonLabel: '',
            runButtonLabels: [
                'app.execute',
                'app.save',
                'app.start',
                'app.submit',
                'app.apply',
                'app.calculate',
                'app.create',
                'app.lay_on',
                'app.update',
                'app.overwrite',
                'app.reset',
                'app.undo',
                'app.next_step',
                'app.save_draft',
                'app.continue',
                'app.upload',
                'app.finish',
                'app.stop',
                'app.delete'
            ]
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'categories',
            'action_types'
        ]),
        header() {
            return this.actionType ? 'Aktion bearbeiten' : 'Aktion erstellen';
        },
        title() {
            return this.actionType ? 'Speichern' : 'Erstellen';
        },
        actionType() {
            return this.action_types.find(ele => ele.id === this.actionTypeId);
        },
        showOtherRunButtonLabel() {
            return !this.runButtonLabels.includes(this.data.save_label);
        }
    },
    methods: {
        ...utils, ...mapActions(reduxActions),
        onSave() {
            let method = this.actionType ? 'UpdateActionType' : 'StoreActionType';
            let position = this.ui.modal.data.position ? JSON.stringify(this.ui.modal.data.position) : null;
            let data = {...this.data};

            if (position) {
                data.position = position;
            }

            this.patchDefinition(method, data).then(this.closeModal).catch(() => {
            });
        },
        onIconUpdate(icon) {
            this.data = {
                ...this.data,
                image: icon
            };
        },
        setRunButtonLabel(event) {
            this.data.save_label = event.target.value;
        },
        resetOtherRunButtonLabel() {
            this.otherRunButtonLabel = '';
        },
        onChangeCustomLabel() {
            if (this.showOtherRunButtonLabel) {
                this.data.save_label = 'app.execute'
            } else {
                this.data.save_label = ''
            }
        }
    },
    watch: {
        data: {
            handler: function (val, oldVal) {
                if (this.ui.errorCode) {
                    this.clearError();
                }
            },
            deep: true
        }
    },
    mounted() {
        // Neue Aktion
        if (!this.ui.modal.data.actionTypeId && this.categories.length) {
            // Workflow-Kategorie & Blitz-Icon als Standard
            this.data.category_id = this.categories[0].id;
            this.data.image = 'flash_on';
            this.data.save_label = 'app.execute';
            this.data.name = 'Aktion ' + (this.action_types.length + 1)
        }
        if (this.ui.modal.data.actionTypeId) {
            this.actionTypeId = this.ui.modal.data.actionTypeId;
            this.data = {...this.actionType};
            if (this.showOtherRunButtonLabel) {
                this.otherRunButtonLabel = this.data.save_label;
            }
        }
    }
};
</script>
