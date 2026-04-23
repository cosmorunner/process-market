<template>
    <div class="modal-dialog modal-lg" role="document" v-if="event">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">{{ addMode ? 'Event anlegen' : 'Event bearbeiten' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-2">
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="event.name" maxlength="40"
                           :readonly="!ui.editable"/>
                    <div v-for="error in (ui.validationErrors.name || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                    <small class="d-block text-muted mb-2">Kleingeschrieben, nur a-z, 0-9 und "_".</small>
                </div>
                <div class="form-group input-group-sm mb-2">
                    <label class="mb-0" for="description">Beschreibung</label>
                    <textarea id="description" class="form-control" name="description" maxlength="200" :readonly="!ui.editable"
                              v-model="event.description">{{event.description}}</textarea>
                    <div v-for="error in (ui.validationErrors.description || [])">
                        <div class="invalid-feedback d-block mt-0">{{ error }}</div>
                    </div>
                </div>
                <div>
                    <label class="mb-1">Daten</label>
                    <table class="table table-sm" v-if="Object.keys(event.data || {}).length">
                        <tbody>
                        <tr v-for="item in ([...event.data || []]).sort((a, b) => a.name > b.name)" class="d-flex">
                            <td class="col-5">{{ item.name }}</td>
                            <td class="col-5">{{ item.description }}</td>
                            <td class="col-2">
                                <button class="float-right btn btn-sm btn-light" @click="onDeleteData(item.name)" v-if="ui.editable">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row" v-if="ui.editable">
                        <div class="col-5">
                            <input type="text" v-model="newName"
                                   class="form-control"
                                   placeholder="Name..."/>
                            <small class="text-muted d-block">Nur a-z, 0-9, Unterstrich</small>
                        </div>
                        <div class="col-5">
                            <textarea class="form-control" placeholder="Beschreibung..." rows="2" v-model="newDescription"></textarea>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-outline-success float-right" @click="onAddData">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Geben Sie Datenfelder an, die der Prozessor "Event auslösen" befüllen kann.</small>
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
import OptionBadges from "../utils/OptionBadges";
import ModalFooter from "./ModalFooter";

export default {
    components: {
        ModalFooter,
        OptionBadges
    },
    data() {
        return {
            event: null,
            addMode: false,
            newName: '',
            newDescription: ''
        };
    },
    computed: {
        ...mapGetters([
            'ui',
            'definition',
        ]),
    },
    methods: {
        ...utils,
        ...mapActions(reduxActions),
        onSave() {
            let method = this.addMode ? 'StoreEvent' : 'UpdateEvent';
            this.patchDefinition(method, this.event).then(this.closeModal).catch(() => {
            });
        },
        onAddData() {
            if (!this.newName.length || !/^[a-z0-9_]+$/.test(this.newName)) {
                return;
            }

            let data = ([...this.event.data || []]).filter(item => item.name !== this.newName);

            this.event.data = [
                ...data,
                {
                    name: this.newName,
                    description: this.newDescription
                }
            ];

            this.newName = '';
            this.newDescription = '';
        },
        onDeleteData(name) {
            this.event.data = [...this.event.data].filter(item => item.name !== name);
        }
    },
    mounted() {
        if (this.ui.modal.data.event) {
            this.event = {...this.ui.modal.data.event};
        }
        else {
            this.addMode = true;
            this.event = {
                name: '',
                description: '',
                data: []
            };
        }
    },
    watch: {
        event: {
            handler() {
                if(this.ui.errorCode) {
                    this.clearError()
                }
            },
            deep: true
        }
    }

};
</script>
