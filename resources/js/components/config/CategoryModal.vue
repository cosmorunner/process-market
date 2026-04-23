<template>
    <div class="modal-dialog" role="document" v-if="category">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between py-2">
                <h5 class="modal-title">{{ addMode ? 'Kategorie anlegen' : 'Kategorie bearbeiten' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="category.name"
                           maxlength="40"/>
                    <div v-for="error in (ui.validationErrors.name || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Beschreibung</label>
                    <textarea id="description" class="form-control" name="description" maxlength="200"
                              v-model="category.description">{{category.description}}</textarea>
                    <div v-for="error in (ui.validationErrors.description || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-0 d-block">Icon</label>
                    <IconSelection :require-selection="false" :selected="category.image"
                                   @on-select-icon="onIconUpdate"/>
                    <div v-for="error in (ui.validationErrors.image || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <div class="dropdown">
                        <label class="mb-0 d-block">Farbe</label>
                        <button class="btn btn-light btn-sm dropdown-toggle border" type="button"
                                id="dropDownColors" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <span v-if="category.color" class="material-icons"
                                  :style="'color:' + category.color + ';'">brightness_1</span>
                            <span v-if="!category.color">?</span>
                        </button>
                        <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropDownColors">
                            <a class="dropdown-item" href="#" @click="category.color = color"
                               v-for="color in colors">
                                <span class="material-icons mi-2x" :style="'color:' + color + ';'">brightness_1</span>
                            </a>
                        </div>
                    </div>
                    <div v-for="error in (ui.validationErrors.color || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-0" for="single">Versteckt?</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="single" :checked="category.hidden"
                               @change="toggleHidden">
                        <label class="custom-control-label" for="single">&nbsp;</label>
                    </div>
                    <small class="text-muted">Versteckte Kategorien werden auf der Web-Oberfläche in der Allisa
                        Plattform nicht angezeigt.</small>
                </div>
            </div>
            <ModalFooter :ui="ui" @save="onSave" :save-label="'Speichern'" />
        </div>
    </div>
</template>

<script>

import {mapActions, mapGetters} from "vuex";
import utils from '../../config-utils';
import {reduxActions} from "../../store/develop-and-config";
import IconSelection from "../utils/IconSelection";
import ModalFooter from "./ModalFooter";

export default {
    components: {
        ModalFooter,
        IconSelection
    },
    data() {
        return {
            category: null,
            addMode: false,
            colors: [
                '#72c6ff',
                '#f1948a',
                '#c39bd3',
                '#bb8fce',
                '#7fb3d5',
                '#85c1e9',
                '#7dcea0',
                '#82e0aa',
                '#f7dc6f',
                '#f0b27a',
                '#edbb99',
                '#e59866',
                '#f4f6f7',
                '#d7dbdd',
                '#aeb6bf'
            ]
        };
    },
    computed: {
        ...mapGetters([
            'ui',
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreCategory' : 'UpdateCategory';
            this.patchDefinition(method, this.category).then(this.closeModal).catch(() => {
            });
        },
        onIconUpdate(icon) {
            this.category = {
                ...this.category,
                image: icon
            };
        },
        toggleHidden() {
            this.category = {
                ...this.category,
                hidden: !this.category.hidden
            };
        },
    },
    mounted() {
        if (this.ui.modal.data.category) {
            this.category = {...this.ui.modal.data.category};
        }
        else {
            this.addMode = true;
            this.category = {
                name: '',
                description: '',
                color: '#72c6ff',
                image: 'flash_on',
                locked: false
            };
        }
    },

};
</script>
